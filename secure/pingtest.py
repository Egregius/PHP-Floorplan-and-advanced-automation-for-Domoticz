#!/usr/bin/env python3
import asyncio
import subprocess
import re
import csv
import os
from datetime import datetime

# --- CONFIGURATIE ---
ROUTER = "Flint2" # Aanpassen bij wissel: "Velop", "Flint2"
DEVICES = {
    "192.168.2.254": "pfSense",
    "192.168.2.101": "living",
    "192.168.2.103": "boven",
    "192.168.2.104": "garage",
    "192.168.2.107": "keuken",
    "192.168.2.201": "Macbook",
}

LOG_DIR = "/temp/ping"
INTERVAL = 30
PING_COUNT = 50

def get_csv_path(ip, name):
    now = datetime.now()
    filename = f"ping_{name}_{ip}_{now.strftime('%H')}_{ROUTER}.csv"
    return os.path.join(LOG_DIR, filename)

async def run_ping_test(ip, name):
    seq = 1
    while True:
        csv_path = get_csv_path(ip, name)

        if not os.path.exists(csv_path):
            with open(csv_path, 'w', newline='') as f:
                writer = csv.writer(f)
                writer.writerow(["seq", "timestamp", "min", "avg", "max", "mdev", "spikes_40"])
            seq = 1

        timestamp = datetime.now().strftime('%H:%M:%S')
        # Stress-test: 50 pings, 0.02 interval
        cmd = ["ping", "-c", str(PING_COUNT), "-s", "1472", "-i", "0.02", "-W", "1", ip]

        try:
            process = await asyncio.create_subprocess_exec(*cmd, stdout=asyncio.subprocess.PIPE, stderr=asyncio.subprocess.PIPE)
            stdout, _ = await process.communicate()
            output = stdout.decode()

            times = [float(t) for t in re.findall(r"time=(\d+\.?\d*) ms", output)]
            # Nieuwe drempelwaarde: 40ms
            spikes = len([t for t in times if t > 40])

            stats_match = re.search(r"rtt min/avg/max/mdev = (\d+\.\d+)/(\d+\.\d+)/(\d+\.\d+)/(\d+\.\d+) ms", output)

            if stats_match:
                rmin, ravg, rmax, rmdev = stats_match.groups()
                with open(csv_path, 'a', newline='') as f:
                    writer = csv.writer(f)
                    writer.writerow([seq, timestamp, rmin, ravg, rmax, rmdev, spikes])
                print(f"[{timestamp}] {name} ({ROUTER}) seq {seq}: avg={ravg}ms, spikes(>40ms)={spikes}")
                seq += 1
        except Exception as e:
            print(f"Fout bij {name}: {e}")

        await asyncio.sleep(INTERVAL)

async def main():
    if not os.path.exists(LOG_DIR): os.makedirs(LOG_DIR, exist_ok=True)
    print(f"🚀 Monitor gestart voor ROUTER: {ROUTER} (Drempel: 40ms)")
    await asyncio.gather(*(run_ping_test(ip, name) for ip, name in DEVICES.items()))

if __name__ == "__main__":
    asyncio.run(main())
