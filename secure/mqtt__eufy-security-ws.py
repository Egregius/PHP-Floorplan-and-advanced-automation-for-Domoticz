#!/usr/bin/env python3
import asyncio
import json
import datetime
import requests
from pathlib import Path
from websockets import connect

# --- Statische Config ---
WS_URL = "ws://192.168.2.26:3000"
CONFIG_PATH = Path("/var/www/eufy_config.json")
RECONNECT_DELAY = 5

def log(msg):
    now = datetime.datetime.now().strftime('%d-%m %H:%M:%S')
    print(f"{now} {msg}")

def load_config():
    """Laadt de Telegram credentials uit de externe JSON file."""
    if not CONFIG_PATH.exists():
        log(f"❌ Configbestand niet gevonden op {CONFIG_PATH}")
        return None
    try:
        return json.loads(CONFIG_PATH.read_text())
    except Exception as e:
        log(f"❌ Fout bij laden config: {e}")
        return None

def send_telegram_photo(image_bytes, config):
    """Verstuurt de binaire afbeelding naar alle geconfigureerde CHAT_IDS."""
    token = config.get("TELEGRAM_TOKEN")
    chat_ids = config.get("CHAT_IDS", [])
    url = f"https://api.telegram.org/bot{token}/sendPhoto"
    
    for chat_id in chat_ids:
        try:
            files = {'photo': ('eufy_doorbell.jpg', image_bytes, 'image/jpeg')}
            data = {'chat_id': chat_id, 'caption': '🔔 Deurbel event (Eufy)'}
            response = requests.post(url, files=files, data=data, timeout=10)
            if response.status_code == 200:
                log(f"✅ Foto verstuurd naar {chat_id}")
            else:
                log(f"❌ Telegram fout ({chat_id}): {response.text}")
        except Exception as e:
            log(f"⚠️ Fout bij versturen naar {chat_id}: {e}")

async def handle_eufy():
    config = load_config()
    if not config:
        return

    log(f"🚀 Script gestart. Luisteren op {WS_URL}...")
    
    while True:
        try:
            async with connect(WS_URL, ping_interval=20) as ws:
                log("✅ Verbonden met Eufy Security WS")
                
                await ws.send(json.dumps({"command": "set_api_schema", "schemaVersion": 13}))
                await ws.send(json.dumps({"command": "start_listening"}))
                
                async for message in ws:
                    data = json.loads(message)
                    
                    if data.get("type") == "event":
                        event_data = data.get("event", {})
                        
                        # Zoek naar de property change van de afbeelding
                        if event_data.get("name") == "picture":
                            log("📸 Afbeelding ontvangen in buffer")
                            
                            raw_data = event_data.get("value", {}).get("data", [])
                            if raw_data:
                                image_bytes = bytes(raw_data)
                                log(f"🖼️ Verwerken ({len(image_bytes)} bytes)...")
                                send_telegram_photo(image_bytes, config)
                            else:
                                log("⚠️ Picture event was leeg.")

        except Exception as e:
            log(f"❌ Verbinding verbroken: {e}")
            await asyncio.sleep(RECONNECT_DELAY)

if __name__ == "__main__":
    try:
        asyncio.run(handle_eufy())
    except KeyboardInterrupt:
        log("👋 Gestopt")
    except Exception as e:
        log(f"☢️ Fatale fout: {e}")