#!/usr/bin/env python3
import requests
from pathlib import Path
import json

TOKEN_FILE = Path("tokens.json")
DEVICES = [
    {"name": "p1meter", "host": "p1dongle"},
    {"name": "kwh",     "host": "energy"},
]

def load_tokens():
    if TOKEN_FILE.exists():
        return json.loads(TOKEN_FILE.read_text())
    return {}

def save_tokens(tokens):
    TOKEN_FILE.write_text(json.dumps(tokens, indent=2))

def request_token(host, client_name="mqtt_script", client_type="script"):
    url = f"https://{host}/api/v2/apitoken"
    data = {"client_name": client_name, "client_type": client_type}
    resp = requests.post(url, json=data, verify=False, timeout=5)
    resp.raise_for_status()
    return resp.json()["token"]

tokens = load_tokens()
for dev in DEVICES:
    name = dev["name"]
    host = dev["host"]
    if name not in tokens:
        token = request_token(host)
        tokens[name] = token
        print(f"{name} token:", token)
save_tokens(tokens)
