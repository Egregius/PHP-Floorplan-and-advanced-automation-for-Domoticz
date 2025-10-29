#!/usr/bin/env python3
import asyncio
import json
import ssl
import time
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
    {"name": "p", "host": "p1dongle"},
    {"name": "z", "host": "energymeter"},
    {"name": "b", "host": "battery"},
]

RECONNECT_DELAY = 5
last_values = {}

def log(*args):
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}]", *args)

def load_tokens():
    return json.loads(TOKEN_FILE.read_text()) if TOKEN_FILE.exists() else {}

def save_tokens(tokens):
    TOKEN_FILE.write_text(json.dumps(tokens, indent=2))

def request_token(host, timeout=60):
    url = f"https://{host}/api/user"
    payload = {"name": "local/homewizard_mqtt", "type": "script"}
    start = time.time()
    log(f"🔑 Token aanvragen bij {host} - DRUK OP KNOP")
    while time.time() - start < timeout:
        try:
            resp = requests.post(url, json=payload, timeout=3, verify=False)
            if resp.status_code == 200 and (token := resp.json().get("token")):
                log(f"✅ Token ontvangen van {host}")
                return token
        except:
            pass
        time.sleep(1)
    raise TimeoutError(f"❌ Timeout na {timeout}s")

class MqttPublisher:
    def __init__(self):
        self.client = mqtt.Client()
        self.client.username_pw_set(MQTT_USER, MQTT_PASS)
        self.client.on_connect = lambda c, u, f, rc: log(f"✅ MQTT verbonden") if rc == 0 else log(f"❌ MQTT fout {rc}")
        self.connected = False
        self.client.on_connect = self._on_connect

    def _on_connect(self, client, userdata, flags, rc):
        self.connected = rc == 0

    def connect(self):
        self.client.connect(MQTT_HOST, MQTT_PORT, 60)
        self.client.loop_start()

    def publish(self, topic, value):
        if self.connected:
            self.client.publish(f"{MQTT_TOPIC}/{topic}", json.dumps(value), retain=True)

def publish_if_changed(mqtt_pub, key, value, transform=None):
    if value is None:
        return
    if transform:
        value = transform(value)
    if last_values.get(key) != value:
        last_values[key] = value
        mqtt_pub.publish(key, value)

def process_measurement(name, data, mqtt_pub):
    if "p" in name:
        publish_if_changed(mqtt_pub, f"n", data.get("power_w"), lambda x: int(round(x)))
        publish_if_changed(mqtt_pub, f"a", data.get("average_power_15m_w"), lambda x: int(round(x)))
    elif "b" in name:
        publish_if_changed(mqtt_pub, f"b", data.get("power_w"), lambda x: int(round(x)))
        publish_if_changed(mqtt_pub, f"c", data.get("state_of_charge_pct"), lambda x: int(round(x)))
    else:
        publish_if_changed(mqtt_pub, f"z", data.get("power_w"), lambda x: -int(round(x)))

async def handle_device(device, token, mqtt_pub, ssl_context):
    name, host = device["name"], device["host"]
    url = f"wss://{host}/api/ws"
    log(f"🔌 {name}: Verbinden met {url}")
    while True:
        try:
            async with websockets.connect(url, ssl=ssl_context, ping_interval=20) as ws:
                log(f"✅ {name}: Verbonden")
                async for message in ws:
                    try:
                        data = json.loads(message)
                        msg_type = data.get("type")
                        if msg_type == "authorization_requested":
                            await ws.send(json.dumps({"type": "authorization", "data": token}))
                            await ws.send(json.dumps({"type": "subscribe", "data": "measurement"}))
                            log(f"🔐 {name}: Geautoriseerd")
                        elif msg_type == "measurement":
                            process_measurement(name, data.get("data", {}), mqtt_pub)
                        elif msg_type == "error":
                            log(f"❌ {name}: {data.get('message', data)}")
                    except json.JSONDecodeError:
                        log(f"⚠️  {name}: Ongeldig JSON")
                    except Exception as e:
                        log(f"⚠️  {name}: {e}")
        except Exception as e:
            log(f"❌ {name}: {e}")
        await asyncio.sleep(RECONNECT_DELAY)

async def main():
    log("🚀 HomeWizard Energy MQTT Bridge")
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
        if dev["name"] not in tokens:
            try:
                tokens[dev["name"]] = request_token(dev["host"])
                save_tokens(tokens)
            except Exception as e:
                log(f"❌ {dev['name']}: {e}")
    tasks = [
        asyncio.create_task(handle_device(dev, tokens[dev["name"]], mqtt_pub, ssl_context))
        for dev in DEVICES if dev["name"] in tokens
    ]
    if tasks:
        await asyncio.gather(*tasks)
    else:
        log("❌ Geen devices beschikbaar")

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        log("👋 Gestopt")
    except Exception as e:
        log(f"❌ Fatale fout: {e}")
        raise