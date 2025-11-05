#!/usr/bin/env python3
import asyncio
import json
import ssl
import time
from datetime import datetime
from pathlib import Path
import requests
import websockets

# Tokens JSON
TOKEN_FILE = Path("/var/www/html/secure/tokens.json")
# Cache bestand (tmpfs)
CACHE_FILE = Path("/dev/shm/cache/en.txt")

# Devices configuratie
DEVICES = [
    {"name": "p", "host": "p1dongle"},
    {"name": "z", "host": "energymeter"},
    {"name": "b", "host": "battery"},
]

RECONNECT_DELAY = 5
state = {"n": 0, "a": 0, "z": 0, "b": 0, "c": 0}

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

# Schrijf huidige state naar tmpfs
def flush_state():
    try:
        CACHE_FILE.write_text(json.dumps(state))
    except Exception as e:
        log("Fout bij schrijven cache:", e)

# Update value en schrijf alleen bij verandering
def update_state(key, value):
    if value is None:
        return
    if state.get(key) != value:
        state[key] = value
        flush_state()

# Process measurement data
def process_measurement(name, data):
    if "p" in name:
        update_state("n", int(round(data.get("power_w", 0))))
        update_state("a", int(round(data.get("average_power_15m_w", 0))))
    elif "b" in name:
        update_state("b", int(round(data.get("power_w", 0))))
        update_state("c", int(round(data.get("state_of_charge_pct", 0))))
    else:
        update_state("z", -int(round(data.get("power_w", 0))))

# Handlers per device
async def handle_device(device, token, ssl_context):
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
                            process_measurement(name, data.get("data", {}))
                        elif msg_type == "error":
                            log(f"❌ {name}: {data.get('message', data)}")
                    except json.JSONDecodeError:
                        log(f"⚠️  {name}: Ongeldig JSON")
                    except Exception as e:
                        log(f"⚠️  {name}: {e}")
        except Exception as e:
            log(f"❌ {name}: {e}")
        await asyncio.sleep(RECONNECT_DELAY)

# Main routine
async def main():
    log("🚀 HomeWizard Energy TMPFS Bridge")
    ssl_context = ssl.create_default_context()
    ssl_context.check_hostname = False
    ssl_context.verify_mode = ssl.CERT_NONE
    try:
        import urllib3
        urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
    except:
        pass

    tokens = load_tokens()
    for dev in DEVICES:
        if dev["name"] not in tokens:
            try:
                tokens[dev["name"]] = request_token(dev["host"])
                save_tokens(tokens)
            except Exception as e:
                log(f"❌ {dev['name']}: {e}")

    tasks = [
        asyncio.create_task(handle_device(dev, tokens[dev["name"]], ssl_context))
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
