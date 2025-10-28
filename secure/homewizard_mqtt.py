#!/usr/bin/env python3

import asyncio
import json
import ssl
from datetime import datetime
from pathlib import Path
import requests
import paho.mqtt.client as mqtt
import websockets

MQTT_HOST = "192.168.2.26"
MQTT_PORT = 1883
MQTT_USER = "mqtt"
MQTT_PASS = "mqtt"
MQTT_TOPIC = "energy"
TOKEN_FILE = Path("/var/www/html/secure/tokens.json")

DEVICES = [
    {"name": "p1meter", "host": "p1dongle"},
    {"name": "kwh", "host": "energymeter"},
    {"name": "batterij", "host": "battery"},
]

HEARTBEAT_INTERVAL = 25
RECONNECT_DELAY = 5

def log(*args):
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}]", *args)

def load_tokens():
    if TOKEN_FILE.exists():
        try:
            return json.loads(TOKEN_FILE.read_text())
        except:
            return {}
    return {}

def save_tokens(tokens):
    TOKEN_FILE.write_text(json.dumps(tokens, indent=2))

def request_token(host, timeout=60):
    url = f"https://{host}/api/user"
    log(f"🔑 Token aanvragen bij {host}")

    payload = {"name": "local/homewizard_mqtt", "type": "script"}
    start = datetime.now().timestamp()

    while datetime.now().timestamp() - start < timeout:
        try:
            resp = requests.post(url, json=payload, timeout=3, verify=False)
            if resp.status_code == 200:
                data = resp.json()
                token = data.get("token")
                if token:
                    log(f"✅ Token ontvangen van {host}")
                    return token
            # Wachten en opnieuw proberen
        except Exception:
            log(f"   Wachten op autorisatie...")
        asyncio.sleep(1)
    raise TimeoutError(f"❌ Timeout: knop niet ingedrukt binnen {timeout} seconden")

class MqttPublisher:
    def __init__(self):
        self.client = mqtt.Client()
        self.client.username_pw_set(MQTT_USER, MQTT_PASS)
        self.client.on_connect = self._on_connect
        self.connected = False

    def _on_connect(self, client, userdata, flags, rc):
        self.connected = rc == 0
        if self.connected:
            log(f"✅ MQTT verbonden met {MQTT_HOST}:{MQTT_PORT}")
        else:
            log(f"❌ MQTT verbinding mislukt (code {rc})")

    def connect(self):
        try:
            self.client.connect(MQTT_HOST, MQTT_PORT, 60)
            self.client.loop_start()
        except Exception as e:
            log(f"❌ MQTT fout: {e}")

    def publish(self, device_name, data):
        if not self.connected:
            log(f"⚠️  MQTT niet verbonden, skip publish voor {device_name}")
            return
        topic = f"{MQTT_TOPIC}/{device_name}"
        payload = json.dumps(data)
        self.client.publish(topic, payload, retain=True)
        log(f"📤 {topic}: {payload}")

async def handle_device(device, token, mqtt_pub, ssl_context):
    name = device["name"]
    host = device["host"]
    url = f"wss://{host}/api/ws"
    log(f"🔌 {name}: Verbinden met {url}")

    while True:
        try:
            async with websockets.connect(url, ssl=ssl_context, ping_interval=None) as ws:
                log(f"✅ {name}: WebSocket verbonden")
                authorized = False

                async for message in ws:
                    data = json.loads(message)
                    log(f"ℹ️  {name}: {data}")
                    msg_type = data.get("type")

                    if msg_type == "authorization_requested" and not authorized:
                        # De juiste payload voor HomeWizard Energy!
                        await ws.send(json.dumps({
                            "type": "authorization",
                            "data": token
                        }))
                        authorized = True
                        log(f"🔐 {name}: Token verzonden")

                        # Direct na autorisatie: subscribe op measurements
                        await ws.send(json.dumps({
                            "type": "subscribe",
                            "data": "measurement"
                        }))
                        log(f"📝 {name}: Subscribe gestuurd")

                        asyncio.create_task(send_heartbeat(ws, name))

                    elif msg_type == "measurement":
#                        mqtt_pub.publish(name, data.get("data", data))
                        if "p1meter" in name:
                            payload = {
                                "w": data["data"].get("power_w"),
                                "avg": data["data"].get("average_power_15m_w")
                            }
                        elif "batterij" in name:
                        	payload = {
                                "w": int(round(data["data"].get("power_w")))
                            }
                        else:
                            payload = {
                                "w": data["data"].get("power_w")
                            }
                        
                        mqtt_pub.publish(name, payload)

                    elif msg_type == "error":
                        log(f"❌ {name}: Fout ontvangen - {data}")

                log(f"⚠️  {name}: Verbinding gesloten")
        except Exception as e:
            log(f"❌ {name}: Fout - {e}")

        log(f"🔄 {name}: Opnieuw verbinden over {RECONNECT_DELAY} seconden...")
        await asyncio.sleep(RECONNECT_DELAY)

async def send_heartbeat(ws, name):
    try:
        while True:
            await asyncio.sleep(HEARTBEAT_INTERVAL)
            await ws.send(json.dumps({"type": "ping"}))
            log(f"💓 {name}: Heartbeat verzonden")
    except asyncio.CancelledError:
        pass
    except Exception as e:
        log(f"❌ {name}: Heartbeat fout - {e}")

async def main():
    log("🚀 HomeWizard Energy MQTT Bridge gestart")
    ssl_context = ssl.create_default_context()
    ssl_context.check_hostname = False
    ssl_context.verify_mode = ssl.CERT_NONE
    try:
        import urllib3
        urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
    except:
        pass

    mqtt_pub = MqttPublisher()
    mqtt_pub.connect()
    await asyncio.sleep(2)

    tokens = load_tokens()
    for dev in DEVICES:
        name = dev["name"]
        if name not in tokens:
            try:
                tokens[name] = request_token(dev["host"])
                save_tokens(tokens)
            except Exception as e:
                log(f"❌ Token ophalen mislukt voor {name}: {e}")
                continue

    tasks = []
    for dev in DEVICES:
        name = dev["name"]
        if name in tokens:
            task = asyncio.create_task(handle_device(dev, tokens[name], mqtt_pub, ssl_context))
            tasks.append(task)
        else:
            log(f"⚠️  {name}: Geen token, overslaan")

    if not tasks:
        log("❌ Geen devices om te verbinden")
        return
    await asyncio.gather(*tasks)

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        log("\n👋 Gestopt door gebruiker")
    except Exception as e:
        log(f"❌ Fatale fout: {e}")
        raise
