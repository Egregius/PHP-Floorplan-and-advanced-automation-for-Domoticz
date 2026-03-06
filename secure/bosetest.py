#!/usr/bin/env python3
import asyncio
import subprocess
import re
import csv
import os
from datetime import datetime

# --- CONFIGURATIE ---
DEVICES = {
    "192.168.2.101": "living",
    "192.168.2.104": "garage",
    "192.168.2.1":   "router_gateway"
}

LOG_DIR = "/temp"
INTERVAL = 30
PING_COUNT = 50

def get_hour_csv_path(ip, name):
    # Formaat: ping_naam_ip_datum_uur.csv
    now = datetime.now()
    filename = f"ping_{name}_{ip}_{now.strftime('%Y%m%d_%H')}.csv"
    return os.path.join(LOG_DIR, filename)

async def run_ping_test(ip, name):
    while True:
        csv_path = get_hour_csv_path(ip, name)

        # Schrijf header als het bestand van dit uur nog niet bestaat
        if not os.path.exists(csv_path):
            with open(csv_path, 'w', newline='') as f:
                writer = csv.writer(f)
                writer.writerow(["timestamp", "min", "avg", "max", "mdev", "spikes_over_20ms"])

        timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        cmd = ["ping", "-c", str(PING_COUNT), "-s", "1472", "-i", "0.02", "-W", "1", ip]

        try:
            process = await asyncio.create_subprocess_exec(*cmd, stdout=asyncio.subprocess.PIPE, stderr=asyncio.subprocess.PIPE)
            stdout, _ = await process.communicate()
            output = stdout.decode()

            times = [float(t) for t in re.findall(r"time=(\d+\.?\d*) ms", output)]
            spikes = len([t for t in times if t > 20])
            stats_match = re.search(r"rtt min/avg/max/mdev = (\d+\.\d+)/(\d+\.\d+)/(\d+\.\d+)/(\d+\.\d+) ms", output)

            if stats_match:
                rmin, ravg, rmax, rmdev = stats_match.groups()
                with open(csv_path, 'a', newline='') as f:
                    writer = csv.writer(f)
                    writer.writerow([timestamp, rmin, ravg, rmax, rmdev, spikes])
                print(f"[{timestamp}] ✅ {name}: avg={ravg}ms")
        except Exception as e:
            print(f"Fout bij {name}: {e}")

        await asyncio.sleep(INTERVAL)

async def main():
    print(f"🚀 Monitor gestart (Hourly logs in {LOG_DIR})")
    await asyncio.gather(*(run_ping_test(ip, name) for ip, name in DEVICES.items()))

if __name__ == "__main__":
    asyncio.run(main())
