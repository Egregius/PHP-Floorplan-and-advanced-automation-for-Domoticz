#!/usr/bin/env python3
# homewizard_energy_mqtt.py
# Installeer: pip install paho-mqtt websockets requests

import asyncio
import json
import ssl
import time
from datetime import datetime
from pathlib import Path
import requests
import paho.mqtt.client as mqtt
import websockets

# ---------------------------
# CONFIG
# ---------------------------
MQTT_HOST = "192.168.2.26"
MQTT_PORT = 1883
MQTT_USER = "mqtt"
MQTT_PASS = "mqtt"
MQTT_TOPIC = "energy"

TOKEN_FILE = Path("tokens.json")

DEVICES = [
    {"name": "p1meter", "host": "p1dongle"},
    {"name": "kwh", "host": "energymeter"},
]

HEARTBEAT_INTERVAL = 25
TOKEN_TIMEOUT = 60
RECONNECT_DELAY = 5

# ---------------------------
# LOGGING
# ---------------------------
def log(*args):
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}]", *args)

# ---------------------------
# TOKEN MANAGEMENT
# ---------------------------
def load_tokens():
    if TOKEN_FILE.exists():
        try:
            return json.loads(TOKEN_FILE.read_text())
        except:
            return {}
    return {}

def save_tokens(tokens):
    TOKEN_FILE.write_text(json.dumps(tokens, indent=2))

def request_token(host, timeout=TOKEN_TIMEOUT):
    """Vraag een token aan via de HomeWizard API"""
    url = f"https://{host}/api/user"
    
    log(f"🔑 Token aanvragen bij {host}")
    log(f"   DRUK NU OP DE KNOP VAN HET APPARAAT (binnen {timeout} sec)")
    
    payload = {"name": "local/homewizard_mqtt", "type": "script"}
    start = time.time()
    
    while time.time() - start < timeout:
        try:
            resp = requests.post(url, json=payload, timeout=3, verify=False)
            
            if resp.status_code == 200:
                data = resp.json()
                token = data.get("token")
                if token:
                    log(f"✅ Token ontvangen van {host}")
                    return token
            
            # Als status 403: knop nog niet ingedrukt
            time.sleep(1)
            
        except requests.exceptions.RequestException as e:
            log(f"   Wachten op autorisatie... ({int(time.time() - start)}s)")
            time.sleep(1)
    
    raise TimeoutError(f"❌ Timeout: knop niet ingedrukt binnen {timeout} seconden")

# ---------------------------
# MQTT CLIENT
# ---------------------------
class MqttPublisher:
    def __init__(self):
        self.client = mqtt.Client()
        self.client.username_pw_set(MQTT_USER, MQTT_PASS)
        self.client.on_connect = self._on_connect
        self.connected = False
        
    def _on_connect(self, client, userdata, flags, rc):
        if rc == 0:
            self.connected = True
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
        log(f"📤 {topic}: {payload[:100]}...")

# ---------------------------
# WEBSOCKET HANDLER
# ---------------------------
async def handle_device(device, token, mqtt_pub, ssl_context):
    name = device["name"]
    host = device["host"]
    
    # Gebruik wss:// met disabled SSL verificatie
    url = f"wss://{host}/api/ws"
    
    log(f"🔌 {name}: Verbinden met {url}")
    
    while True:
        try:
            async with websockets.connect(url, ssl=ssl_context, ping_interval=None) as ws:
                log(f"✅ {name}: WebSocket verbonden")
                
                # Start heartbeat taak
                heartbeat_task = asyncio.create_task(send_heartbeat(ws, name))
                
                try:
                    async for message in ws:
                        data = json.loads(message)
                        msg_type = data.get("type")
                        
                        if msg_type == "authorization_requested":
                            log(f"🔐 {name}: Autorisatie gevraagd, token versturen...")
                            await ws.send(json.dumps({
                                "type": "authorize",
                                "token": token
                            }))
                        
                        elif msg_type == "authorized":
                            log(f"✅ {name}: Geautoriseerd")
                        
                        elif msg_type == "unauthorized":
                            log(f"❌ {name}: Autorisatie geweigerd - token mogelijk verlopen!")
                            log(f"   Verwijder tokens.json en start opnieuw om nieuwe tokens aan te vragen")
                        
                        elif msg_type == "error":
                            log(f"❌ {name}: Fout ontvangen - {data}")
                        
                        elif msg_type == "update":
                            # Publiceer data naar MQTT
                            mqtt_pub.publish(name, {
                                "device": name,
                                "timestamp": datetime.now().isoformat(),
                                "data": data.get("data", data)
                            })
                        
                        elif msg_type == "pong":
                            # Heartbeat response
                            pass
                        
                        else:
                            log(f"📨 {name}: Onbekend bericht type '{msg_type}': {data}")
                
                except websockets.exceptions.ConnectionClosed:
                    log(f"⚠️  {name}: Verbinding gesloten")
                finally:
                    heartbeat_task.cancel()
        
        except Exception as e:
            log(f"❌ {name}: Fout - {e}")
        
        log(f"🔄 {name}: Opnieuw verbinden over {RECONNECT_DELAY} seconden...")
        await asyncio.sleep(RECONNECT_DELAY)

async def send_heartbeat(ws, name):
    """Stuur periodiek een ping naar de websocket"""
    try:
        while True:
            await asyncio.sleep(HEARTBEAT_INTERVAL)
            await ws.send(json.dumps({"type": "ping"}))
            log(f"💓 {name}: Heartbeat verzonden")
    except asyncio.CancelledError:
        pass
    except Exception as e:
        log(f"❌ {name}: Heartbeat fout - {e}")

# ---------------------------
# MAIN
# ---------------------------
async def main():
    log("🚀 HomeWizard Energy MQTT Bridge gestart")
    
    # SSL context voor self-signed certificaten
    ssl_context = ssl.create_default_context()
    ssl_context.check_hostname = False
    ssl_context.verify_mode = ssl.CERT_NONE
    
    # Suppress SSL warnings
    try:
        import urllib3
        urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
    except:
        pass
    
    # MQTT setup
    mqtt_pub = MqttPublisher()
    mqtt_pub.connect()
    
    # Wacht even tot MQTT verbonden is
    await asyncio.sleep(2)
    
    # Tokens laden of aanvragen
    tokens = load_tokens()
    
    for dev in DEVICES:
        name = dev["name"]
        if name not in tokens:
            try:
                log(f"\n{'='*60}")
                tokens[name] = request_token(dev["host"])
                save_tokens(tokens)
                log(f"{'='*60}\n")
            except Exception as e:
                log(f"❌ Token ophalen mislukt voor {name}: {e}")
                continue
    
    # Start websocket tasks voor elk device
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
    
    # Draai alle tasks tegelijk
    await asyncio.gather(*tasks)

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        log("\n👋 Gestopt door gebruiker")
    except Exception as e:
        log(f"❌ Fatale fout: {e}")
        raise