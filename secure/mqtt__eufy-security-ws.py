#!/usr/bin/env python3
import asyncio
import json
import datetime
import requests
import time
import os
from pathlib import Path
from websockets import connect

# --- Config ---
WS_URL = "ws://192.168.2.26:3000"
CONFIG_PATH = Path("/var/www/eufy_config.json")
SUPPRESS_FILE = Path("/dev/shm/cache/timestampweg.txt")
RECONNECT_DELAY = 5
MAX_TELEGRAM_RETRIES = 6
SUPPRESS_WINDOW = 300  # 5 minuten in seconden

def log(msg):
    now = datetime.datetime.now().strftime('%d-%m %H:%M:%S')
    print(f"{now} {msg}")

def is_suppressed():
    """Checkt of we het sturen van foto's moeten onderdrukken."""
    if not SUPPRESS_FILE.exists():
        return False
    
    try:
        content = SUPPRESS_FILE.read_text().strip()
        if not content:
            return False
        
        file_ts = float(content)
        current_ts = time.time()
        
        # Als timestamp minder dan 300 sec geleden is -> onderdrukken
        if (current_ts - file_ts) < SUPPRESS_WINDOW:
            return True
    except Exception as e:
        log(f"⚠️ Fout bij lezen suppress file: {e}")
    
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
    """Verstuurt foto naar Telegram met retry-logica."""
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
                    if attempt < MAX_TELEGRAM_RETRIES:
                        time.sleep(2)
            except Exception as e:
                log(f"❌ Netwerkfout Telegram ({chat_id}), poging {attempt}: {e}")
                if attempt < MAX_TELEGRAM_RETRIES:
                    time.sleep(2)
        
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
                    if data.get("type") == "event":
                        event_data = data.get("event", {})
                        
                        if event_data.get("name") == "ringing" and event_data.get("value") is True:
                            if is_suppressed():
                                log("🔕 Aanbellen gedetecteerd, maar onderdrukt via timestamp file.")
                            else:
                                log("🔔 Er wordt aangebeld! (Wachten op foto...)")

                        if event_data.get("name") == "picture":
                            if is_suppressed():
                                log("📸 Foto ontvangen, maar verzenden overgeslagen (onderdrukt).")
                                continue

                            log("📸 Foto ontvangen, verwerken...")
                            val = event_data.get("value", {})
                            inner = val.get("data", {}) if isinstance(val, dict) else {}
                            buffer_list = inner.get("data") if isinstance(inner, dict) else None
                            
                            if buffer_list:
                                try:
                                    image_bytes = bytes([int(x) for x in buffer_list])
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