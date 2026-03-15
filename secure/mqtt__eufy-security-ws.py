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
    log(f"🔍 Laden config van {CONFIG_PATH}...")
    if not CONFIG_PATH.exists():
        log(f"❌ Configbestand NIET gevonden!")
        return None
    try:
        data = json.loads(CONFIG_PATH.read_text())
        log(f"✅ Config geladen. CHAT_IDS: {data.get('CHAT_IDS')}")
        return data
    except Exception as e:
        log(f"❌ JSON parse fout in config: {e}")
        return None

def send_telegram_photo(image_bytes, config):
    token = config.get("TELEGRAM_TOKEN")
    chat_ids = config.get("CHAT_IDS", [])
    url = f"https://api.telegram.org/bot{token}/sendPhoto"
    
    for chat_id in chat_ids:
        try:
            log(f"📤 Telegram upload starten voor {chat_id}...")
            files = {'photo': ('eufy_doorbell.jpg', image_bytes, 'image/jpeg')}
            data = {'chat_id': chat_id, 'caption': f'🔔 Deurbel ({datetime.datetime.now().strftime("%H:%M:%S")})'}
            response = requests.post(url, files=files, data=data, timeout=15)
            log(f"📨 Telegram response ({chat_id}): {response.status_code} - {response.text}")
        except Exception as e:
            log(f"⚠️ Telegram HTTP fout: {e}")

async def heartbeat():
    """Blijf loggen dat het script nog draait."""
    while True:
        await asyncio.sleep(60)
        log("💓 Heartbeat: Script luistert nog naar WebSocket...")

async def handle_eufy():
    config = load_config()
    if not config:
        log("🚫 Script stopt: Geen geldige configuratie.")
        return

    asyncio.create_task(heartbeat())
    
    while True:
        try:
            log(f"🔌 Verbinden met {WS_URL}...")
            async with connect(WS_URL, ping_interval=20, ping_timeout=20) as ws:
                log("✅ WebSocket verbinding succesvol!")
                
                # Handshake
                log("🤝 API Schema 13 instellen...")
                await ws.send(json.dumps({"command": "set_api_schema", "schemaVersion": 13}))
                
                log("👂 Start listening command verzenden...")
                await ws.send(json.dumps({"command": "start_listening"}))
                
                async for message in ws:
                    data = json.loads(message)
                    msg_type = data.get("type")
                    
                    # Debug: Log elk binnenkomend bericht type
                    if msg_type != "event":
                         log(f"📩 Ontvangen message type: {msg_type}")

                    if msg_type == "event":
                        event_data = data.get("event", {})
                        event_name = event_data.get("event")
                        prop_name = event_data.get("name")
                        
                        log(f"🔔 Event ontvangen: {event_name} | Property: {prop_name}")
                        
                        # De 'picture' property is wat we zoeken
                        if prop_name == "picture":
                            log("📸 AFBEELDING GEVONDEN! Buffer verwerken...")
                            raw_data = event_data.get("value", {}).get("data", [])
                            
                            if raw_data:
                                image_bytes = bytes(raw_data)
                                log(f"🖼️ Byte-count: {len(image_bytes)}. Verzenden naar Telegram...")
                                send_telegram_photo(image_bytes, config)
                            else:
                                log("⚠️ Buffer was leeg of ontbreekt.")
                                
        except Exception as e:
            log(f"❌ Verbinding verbroken of fout: {type(e).__name__} - {e}")
            log(f"🔄 Reconnect over {RECONNECT_DELAY}s...")
            await asyncio.sleep(RECONNECT_DELAY)

if __name__ == "__main__":
    try:
        asyncio.run(handle_eufy())
    except KeyboardInterrupt:
        log("👋 Handmatig gestopt.")