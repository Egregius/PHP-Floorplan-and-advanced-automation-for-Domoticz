#!/usr/bin/env python3
import asyncio
import json
import logging
import datetime
from websockets import connect
import paho.mqtt.client as mqtt

# --- Config ---
WS_URL = "ws://192.168.2.26:3000"
MQTT_HOST = "192.168.30.22"
MQTT_TOPIC = "d/e/doorbell/picture_url"
MQTT_USER = "mqtt"
MQTT_PASS = "mqtt"

# --- Logger ---
def log(msg):
    now = datetime.datetime.now().strftime('%d-%m %H:%M:%S')
    print(f"{now} {msg}")

# --- MQTT Setup ---
mqtt_client = mqtt.Client(client_id="eufy_picture_bridge")
mqtt_client.username_pw_set(MQTT_USER, MQTT_PASS)

async def handle_eufy():
    log(f"🔌 Verbinden met Eufy WS: {WS_URL}")
    
    while True:
        try:
            async with connect(WS_URL, ping_interval=20) as ws:
                log("✅ Verbonden met Eufy WS")
                
                # 1. Zet API schema (nodig voor nieuwere bropat versies)
                await ws.send(json.dumps({"command": "set_api_schema", "schemaVersion": 13}))
                
                # 2. Start met luisteren naar events
                await ws.send(json.dumps({"command": "start_listening"}))
                
                async for message in ws:
                    data = json.loads(message)
                    msg_type = data.get("type")

                    # We zoeken naar 'event' messages van de camera/deurbel
                    if msg_type == "event":
                        event_data = data.get("event", {})
                        event_name = event_data.get("event")
                        
                        # Debug: log alle event namen om te zien wat er binnenkomt
                        log(f"Event ontvangen: {event_name}")

                        # Check specifiek op picture_url updates
                        if "picture_url" in event_data:
                            url = event_data.get("picture_url")
                            log(f"📸 Nieuwe URL gevonden: {url}")
                            
                            if mqtt_client.connect(MQTT_HOST, 1883, 60) == 0:
                                mqtt_client.publish(MQTT_TOPIC, url, retain=True, qos=1)
                                mqtt_client.disconnect()
                                log(f"📡 URL gepubliceerd naar MQTT")

        except Exception as e:
            log(f"❌ Fout in verbinding: {e}")
            await asyncio.sleep(5)

if __name__ == "__main__":
    try:
        asyncio.run(handle_eufy())
    except KeyboardInterrupt:
        log("👋 Gestopt door gebruiker")