#!/usr/bin/env python3
import asyncio
import json
import ssl
import time
from datetime import datetime
from pathlib import Path
import requests
import websockets
import paho.mqtt.client as mqtt


MQTT_HOST = "192.168.2.22"
MQTT_PORT = 1883
MQTT_USER = "mqtt"
MQTT_PASS = "mqtt"
MQTT_BASE = "d"

mqtt_client = mqtt.Client(client_id="homewizard_bridge")
mqtt_client.username_pw_set(MQTT_USER, MQTT_PASS)
mqtt_client.connect(MQTT_HOST, MQTT_PORT, 60)
mqtt_client.loop_start()


# Tokens JSON
TOKEN_FILE = Path("/var/www/html/secure/tokens.json")

# Cache bestanden (tmpfs)
CACHE_FILE = Path("/dev/shm/cache/en.txt")
TELLER_FILE = Path("/dev/shm/cache/teller.txt")

# Devices configuratie
DEVICES = [
    {"name": "p", "host": "p1dongle"},
    {"name": "z", "host": "energymeter"},
    {"name": "b", "host": "battery"},
]

RECONNECT_DELAY = 5
state = {"n": 0, "a": 0, "z": 0, "b": 0, "c": 0}
teller_state = {"import": 0, "export": 0, "gas": 0, "water": 0}

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

def flush_state():
    try:
        CACHE_FILE.write_text(json.dumps(state))
    except Exception as e:
        log("Fout bij schrijven cache:", e)

def flush_teller_state():
    try:
        TELLER_FILE.write_text(json.dumps(teller_state))
    except Exception as e:
        log("Fout bij schrijven teller cache:", e)

def update_state(key, value):
    if value is None:
        return

    if state.get(key) != value:
        state[key] = value

        # tmpfs: volledige state
        flush_state()

        # MQTT: enkel deze key
        mqtt_publish_key(key, value)



def update_teller(import_kwh, export_kwh, gas, water):
    if import_kwh is not None and teller_state["import"] != import_kwh:
        teller_state["import"] = import_kwh
        mqtt_publish_teller("import", import_kwh)

    if export_kwh is not None and teller_state["export"] != export_kwh:
        teller_state["export"] = export_kwh
        mqtt_publish_teller("export", export_kwh)

    if gas is not None and teller_state["gas"] != gas:
        teller_state["gas"] = gas
        mqtt_publish_teller("gas", gas)

    if water is not None and teller_state["water"] != water:
        teller_state["water"] = water
        mqtt_publish_teller("water", water)

    # tmpfs blijft altijd volledige teller-state
    flush_teller_state()


def process_measurement(name, data):
    if name == "p":
        update_state("n", int(round(data.get("power_w", 0))))
        update_state("a", int(round(data.get("average_power_15m_w", 0))))

        # teller metingen
        import_kwh = data.get("energy_import_kwh")
        export_kwh = data.get("energy_export_kwh")

        gas = None
        water = None

        for ext in data.get("external", []):
            if ext.get("type") == "gas_meter":
                gas = ext.get("value")
            elif ext.get("type") == "water_meter":
                water = ext.get("value")

        update_teller(import_kwh, export_kwh, gas, water)

    elif name == "b":
        update_state("b", int(round(data.get("power_w", 0))))
        update_state("c", int(round(data.get("state_of_charge_pct", 0))))
    else:
        update_state("z", -int(round(data.get("power_w", 0))))

def mqtt_publish_key(key, value):
    try:
        topic = f"d/{key}"
        payload = {key: value}
        if key == "n":
            payload["t"] = int(time.time())
        mqtt_client.publish(topic, json.dumps(payload))
    except Exception as e:
        log("MQTT fout:", e)

def mqtt_publish_teller(key, value):
    try:
        topic = f"teller/{key}"
        payload = {key: value}
        mqtt_client.publish(topic, json.dumps(payload))
    except Exception as e:
        log("MQTT fout:", e)

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
                    except Exception as e:
                        log(f"⚠️  {name}: {e}")
        except Exception as e:
            log(f"❌ {name}: {e}")
        await asyncio.sleep(RECONNECT_DELAY)

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

    tasks = [asyncio.create_task(handle_device(dev, tokens[dev["name"]], ssl_context))
             for dev in DEVICES if dev["name"] in tokens]

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
