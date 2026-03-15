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

                    # Log elk event voor debugging
                    if msg_type == "event":
                        event_data = data.get("event", {})
                        event_name = event_data.get("event")
                        
                        # Volledige dump van het event voor inspectie
                        log(f"🔔 Event: {event_name} | Data: {json.dumps(event_data)}")

                        # Check specifiek op picture_url in de event data
                        # Soms zit het in 'event_data', soms dieper in 'properties'
                        url = event_data.get("picture_url")
                        
                        if url:
                            log(f"📸 URL gedetecteerd: {url}")
                            
                            try:
                                # Gebruik een korte timeout voor de MQTT publish
                                mqtt_client.connect(MQTT_HOST, MQTT_PORT, 10)
                                mqtt_client.publish(MQTT_TOPIC, url, retain=True, qos=1)
                                mqtt_client.disconnect()
                                log(f"📡 URL succesvol naar MQTT gepushed")
                            except Exception as mqtt_err:
                                log(f"❌ MQTT Publish fout: {mqtt_err}")
        except Exception as e:
            log(f"❌ Fout in verbinding: {e}")
            await asyncio.sleep(5)

if __name__ == "__main__":
    try:
        asyncio.run(handle_eufy())
    except KeyboardInterrupt:
        log("👋 Gestopt door gebruiker")