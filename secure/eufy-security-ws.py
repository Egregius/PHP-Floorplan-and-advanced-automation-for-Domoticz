import asyncio
import websockets
import json

async def get_eufy_url():
    uri = "ws://192.168.2.26:3000" # IP van je add-on
    async with websockets.connect(uri) as websocket:
        # Registreer voor events (indien nodig voor jouw versie)
        await websocket.send(json.dumps({
            "command": "start_listening"
        }))
        
        while True:
            msg = await websocket.recv()
            data = json.loads(msg)
            # Zoek naar de picture_url in de event data
            if "picture_url" in str(data):
                print(f"URL gevonden: {data['event']['picture_url']}")
                # Hier kun je je Telegram push triggeren

asyncio.get_event_loop().run_until_complete(get_eufy_url())