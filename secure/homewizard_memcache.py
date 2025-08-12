#!/usr/bin/env python3
import argparse
import asyncio
import json
import os
import time
from pathlib import Path
import requests
import websockets
from pymemcache.client import base as memcache

TOKEN_FILE = Path.home() / ".homewizard_token_{ip}.json"
HEADERS = {"Content-Type": "application/json", "X-Api-Version": "2"}

def save_token(ip, name, token):
    p = Path(str(TOKEN_FILE).format(ip=ip.replace('.', '_')))
    p.write_text(json.dumps({"name": name, "token": token}))
    os.chmod(p, 0o600)

def load_token(ip):
    p = Path(str(TOKEN_FILE).format(ip=ip.replace('.', '_')))
    if p.exists():
        try:
            return json.loads(p.read_text()).get("token")
        except Exception:
            return None
    return None

def request_token(ip, name, insecure=False, timeout=5):
    url = f"https://{ip}/api/user"
    payload = {"name": name}
    verify = not insecure
    print(f"[i] Requesting token from {url} (press button on device)...")
    while True:
        try:
            r = requests.post(url, headers=HEADERS, json=payload, verify=verify, timeout=timeout)
        except requests.exceptions.SSLError:
            raise SystemExit("[!] SSL error. Use --insecure to skip cert validation.")
        except Exception as e:
            print(f"[!] Network error: {e}, retrying...")
            time.sleep(2)
            continue

        if r.status_code == 403:
            print("[i] Press button, retrying...")
            time.sleep(1)
            continue
        elif r.status_code == 200:
            token = r.json().get("token")
            if token:
                print("[+] Got token.")
                save_token(ip, name, token)
                return token
            else:
                raise SystemExit("[-] No token in response.")
        else:
            print(f"[!] HTTP {r.status_code}: {r.text}")
            time.sleep(2)

async def ws_run(ip, token, topic="measurement", insecure=False, mc_host="127.0.0.1", mc_port=11211):
    ws_url = f"wss://{ip}/api/ws"
    ssl_ctx = False if insecure else None
    mc = memcache.Client((mc_host, mc_port))

    while True:
        try:
            print(f"[i] Connecting to {ws_url}...")
            async with websockets.connect(ws_url, ssl=ssl_ctx) as ws:
                # read initial
                try:
                    msg = await asyncio.wait_for(ws.recv(), timeout=5)
                    print("[<] init:", msg)
                except asyncio.TimeoutError:
                    pass
                # authorize
                await ws.send(json.dumps({"type": "authorization", "data": token}))
                resp = await ws.recv()
                print("[<]", resp)
                if json.loads(resp).get("type") != "authorized":
                    print("[!] Auth failed, reconnecting...")
                    continue
                # subscribe
                await ws.send(json.dumps({"type": "subscribe", "data": topic}))
                print(f"[+] Subscribed to {topic}")
                # main loop
                while True:
                    raw = await ws.recv()
                    try:
                        data = json.loads(raw)
                        mc.set("homewizard_p1", json.dumps(data))
                    except:
                        pass
        except Exception as e:
            print(f"[!] Error: {e}, reconnecting...")
            await asyncio.sleep(3)

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--ip", required=True)
    parser.add_argument("--name", required=True)
    parser.add_argument("--topic", default="measurement")
    parser.add_argument("--insecure", action="store_true")
    parser.add_argument("--mc-host", default="127.0.0.1")
    parser.add_argument("--mc-port", type=int, default=11211)
    args = parser.parse_args()

    token = load_token(args.ip) or request_token(args.ip, args.name, insecure=args.insecure)
    asyncio.run(ws_run(args.ip, token, topic=args.topic, insecure=args.insecure,
                       mc_host=args.mc_host, mc_port=args.mc_port))

if __name__ == "__main__":
    main()
