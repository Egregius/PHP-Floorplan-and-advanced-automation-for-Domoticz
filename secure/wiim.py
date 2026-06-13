import asyncio
from pywiim import Player, WiimClient

async def main():
    client = WiimClient("192.168.2.9")
    player = Player(client)
    await player.refresh()
    
    print(f"Device: {player.name} ({player.model_name or player.model})")
    print(f"Playing: {player.play_state}")
    print(f"Volume: {player.volume}")
    
    await player.close()

asyncio.run(main())