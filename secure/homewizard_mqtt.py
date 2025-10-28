#!/usr/bin/env python3
import asyncio, json, ssl, traceback
from datetime import datetime
import websockets, paho.mqtt.client as mqtt

# ---------------------------
# CONFIG
# ---------------------------
MQTT_HOST = "192.168.2.26"
MQTT_PORT = 1883
MQTT_USER = "mqtt"
MQTT_PASS = "mqtt"
MQTT_TOPIC_BASE = "energy"

DEVICES = [
    {"name": "p1meter", "url": "wss://p1dongle/api/ws", "token": "005BF7516EE3E996142F7E742823275E"},
    {"name": "kwh", "url": "wss://energymeter/api/ws", "token": "0017DB17E4AC2ADC97E5279AE4142F8D"},
]

VERIFY_SSL = False
HEARTBEAT_INTERVAL = 25
RECONNECT_BASE = 1.0
RECONNECT_MAX = 60.0
MQTT_RETAIN = True

# ---------------------------
def now(): return datetime.now().strftime("%Y-%m-%d %H:%M:%S")
def log(*args): print(f"[{now()}]", *args)

# ---------------------------
class MqttPublisher:
    def __init__(self, host, port, user=None, password=None):
        self.client = mqtt.Client()
        if user and password:
            self.client.username_pw_set(user, password)
        self.client.on_connect = lambda *a: log("MQTT connected")
        self.client.on_disconnect = lambda *a: log("MQTT disconnected")
        self.client.connect(host, port, 60)
        self.client.loop_start()
    def publish(self, topic, payload):
        try:
            if not isinstance(payload, (str, bytes)): payload = json.dumps(payload)
            full_topic = f"{MQTT_TOPIC_BASE}/{topic}".strip("/")
            self.client.publish(full_topic, payload, retain=MQTT_RETAIN)
            log("MQTT publish", full_topic, payload)
        except Exception as e:
            log("MQTT publish error:", e)

# ---------------------------
async def heartbeat(name, ws):
    while True:
        await asyncio.sleep(HEARTBEAT_INTERVAL)
        try:
            await ws.send(json.dumps({"type": "ping"}))
            log(f"{name}: ping")
        except Exception as e:
            log(f"{name}: heartbeat failed: {e}")
            break

# ---------------------------
async def run_device(dev, mqtt, ssl_ctx):
    name, url, token = dev["name"], dev["url"], dev["token"]
    backoff = RECONNECT_BASE

    while True:
        try:
            log(f"{name}: connecting to {url}")
            async with websockets.connect(url, ssl=ssl_ctx, ping_interval=None) as ws:
                log(f"{name}: connected")

                async for msg in ws:
                    try:
                        data = json.loads(msg)
                    except Exception:
                        log(f"{name}: invalid JSON: {msg[:80]!r}")
                        continue

                    t = data.get("type")
                    log(f"{name}: recv {t}")

                    if t == "authorization_requested":
                        auth_msg = {"type": "authorize", "token": token}
                        await ws.send(json.dumps(auth_msg))
                        log(f"{name}: sent token")
                        continue

                    if t in ("authorization_succeeded", "authenticated"):
                        log(f"{name}: authorized OK")
                        asyncio.create_task(heartbeat(name, ws))
                        continue

                    if t == "authorization_failed":
                        log(f"{name}: authorization FAILED -> check token!")
                        continue

                    # Normal data message
                    payload = {"device": name, "timestamp": now(), "raw": data}
                    for k in ("active_power_w","total_power_import_kwh","total_power_export_kwh"):
                        if k in data: payload[k] = data[k]

                    mqtt.publish(name, payload)
                    mqtt.publish("", payload)

        except Exception as e:
            log(f"{name}: exception {e}")
            log(traceback.format_exc())

        log(f"{name}: reconnecting in {backoff:.1f}s")
        await asyncio.sleep(backoff)
        backoff = min(backoff * 2, RECONNECT_MAX)

# ---------------------------
async def main():
    ssl_ctx = ssl.SSLContext(ssl.PROTOCOL_TLS_CLIENT)
    ssl_ctx.check_hostname = False
    ssl_ctx.verify_mode = ssl.CERT_NONE
    ssl_ctx.set_ciphers("DEFAULT")

    mqtt = MqttPublisher(MQTT_HOST, MQTT_PORT, MQTT_USER, MQTT_PASS)
    tasks = [asyncio.create_task(run_device(d, mqtt, ssl_ctx)) for d in DEVICES]
    await asyncio.gather(*tasks)

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        log("Exiting.")
