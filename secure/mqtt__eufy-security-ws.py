#!/usr/bin/env python3
import asyncio
import json
import datetime
import requests
import time
import os
import hashlib
import re
from pathlib import Path
from websockets import connect

# --- Config ---
WS_URL = "ws://192.168.2.26:3000"
CONFIG_PATH = Path("/var/www/eufy_config.json")
SUPPRESS_FILE = Path("/dev/shm/cache/timestampweg.txt")
HASH_FILE = Path("/dev/shm/last_eufy_hash.txt") # Bestand om laatste hash te onthouden
RECONNECT_DELAY = 5
MAX_TELEGRAM_RETRIES = 6
SUPPRESS_WINDOW = 300  # 5 minuten in seconden
last_image_age = None

def log(msg):
    now = datetime.datetime.now().strftime('%d-%m %H:%M:%S')
    print(f"{now} {msg}")

def get_event_age_seconds(data):
    """Haal leeftijd op uit de bestandsnaam in customData, of None als niet beschikbaar."""
    try:
        custom = data.get("event", {}).get("customData", {})
        path = custom.get("command", {}).get("value", "")
        match = re.search(r'(\d{14})_c\d+\.jpg', path)
        if match:
            ts = datetime.strptime(match.group(1), "%Y%m%d%H%M%S")
            return (datetime.now() - ts).total_seconds()
    except Exception as e:
        log(f"⚠️ Fout bij parsen bestandsnaam: {e}")
    return None

def is_suppressed():
    if not SUPPRESS_FILE.exists():
        return False
    try:
        content = SUPPRESS_FILE.read_text().strip()
        if not content: return False
        file_ts = float(content)
        return (time.time() - file_ts) < SUPPRESS_WINDOW
    except Exception as e:
        log(f"⚠️ Fout bij lezen suppress file: {e}")
        return False

def is_duplicate(image_bytes):
    """Checkt of de foto hetzelfde is als de vorige."""
    current_hash = hashlib.md5(image_bytes).hexdigest()
    if HASH_FILE.exists():
        last_hash = HASH_FILE.read_text().strip()
        if last_hash == current_hash:
            return True # Is een duplicaat
    
    # Sla nieuwe hash op
    HASH_FILE.write_text(current_hash)
    return False

def load_config():
    if not CONFIG_PATH.exists():
        log(f"❌ Config niet gevonden: {CONFIG_PATH}")
        return None
    try:
        return json.loads(CONFIG_PATH.read_text())
    except Exception as e:
        log(f"❌ Config laadfout: {e}")
        return None

def send_telegram_photo(image_bytes, config):
    token = config.get("TELEGRAM_TOKEN")
    chat_ids = config.get("CHAT_IDS", [])
    url = f"https://api.telegram.org/bot{token}/sendPhoto"
    
    for chat_id in chat_ids:
        success = False
        attempt = 0
        while not success and attempt < MAX_TELEGRAM_RETRIES:
            attempt += 1
            try:
                files = {'photo': ('eufy_doorbell.jpg', image_bytes, 'image/jpeg')}
                data = {'chat_id': chat_id}
                response = requests.post(url, files=files, data=data, timeout=15)
                if response.status_code == 200:
                    log(f"✅ Foto verzonden naar {chat_id} (poging {attempt})")
                    success = True
                else:
                    log(f"⚠️ Telegram fout {response.status_code} op {chat_id}: {response.text}")
                    if attempt < MAX_TELEGRAM_RETRIES: time.sleep(2)
            except Exception as e:
                log(f"❌ Netwerkfout Telegram ({chat_id}), poging {attempt}: {e}")
                if attempt < MAX_TELEGRAM_RETRIES: time.sleep(2)
        if not success:
            log(f"🛑 Foto definitief mislukt voor {chat_id} na {MAX_TELEGRAM_RETRIES} pogingen.")

async def handle_eufy():
    config = load_config()
    if not config: return

    log(f"🚀 Eufy-Telegram Bridge actief op {WS_URL}")
    
    while True:
        try:
            async with connect(WS_URL, ping_interval=20) as ws:
                log("✅ Verbonden met Eufy WS")
                await ws.send(json.dumps({"command": "set_api_schema", "schemaVersion": 13}))
                await ws.send(json.dumps({"command": "start_listening"}))
                async for message in ws:
                    data = json.loads(message)
                    log(f"data = {data}")
                    if data.get("type") == "event":
                        event_data = data.get("event", {})
                        if (event_data.get("event") == "command result" and
                            event_data.get("customData", {}).get("command", {}).get("name") == "stationDownloadImage"):
                            age = get_event_age_seconds(data)
                            if age is not None:
                                last_image_age = age
                                log(f"⏱️ Volgende foto is {int(age)}s oud")
                        if event_data.get("name") == "picture":
                            if last_image_age is not None and last_image_age > 60:
                                log(f"⏱️ Foto genegeerd, {int(last_image_age)}s oud.")
                                last_image_age = None
                                continue
                            last_image_age = None
                            val = event_data.get("value", {})
                            inner = val.get("data", {}) if isinstance(val, dict) else {}
                            buffer_list = inner.get("data") if isinstance(inner, dict) else None
                            if buffer_list:
                                try:
                                    image_bytes = bytes([int(x) for x in buffer_list])
                                    if is_duplicate(image_bytes):
                                        log("📸 Foto ontvangen, maar identiek aan vorige (duplicaat genegeerd).")
                                        continue
                                    if is_suppressed():
                                        log("📸 Foto ontvangen, maar onderdrukt.")
                                        continue
                                    log("📸 Nieuwe foto verwerken...")
                                    loop = asyncio.get_event_loop()
                                    await loop.run_in_executor(None, send_telegram_photo, image_bytes, config)
                                except Exception as e:
                                    log(f"❌ Fout bij byte-conversie: {e}")

        except Exception as e:
            log(f"🔄 WS Verbinding verbroken ({e}), opnieuw over {RECONNECT_DELAY}s...")
            await asyncio.sleep(RECONNECT_DELAY)

if __name__ == "__main__":
    try:
        asyncio.run(handle_eufy())
    except KeyboardInterrupt:
        log("👋 Gestopt door gebruiker")