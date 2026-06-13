import asyncio
import logging
from pywiim import Player, WiiMClient

# Zet logging aan voor de HTTP-client (aiohttp) zodat we de exacte requests zien
logging.basicConfig(level=logging.DEBUG)
logging.getLogger("aiohttp.client").setLevel(logging.DEBUG)

async def noop(*args, **kwargs):
    pass

async def main():
    client = WiiMClient("192.168.2.9")
    player = Player(client)
    player._ensure_upnp_client = noop
    
    url = "https://home.egregius.be/sounds/doorbell.mp3"
    
    try:
        # Dit triggerde zojuist het succesvolle geluid
        await player.play_notification(url)
    except Exception as e:
        print(f"Fout: {e}")
        
    await client.close()

asyncio.run(main())