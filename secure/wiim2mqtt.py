#!/usr/init/env python3
import sys
import os
import time
import json
import threading
import requests
import html
import xml.etree.ElementTree as ET
from datetime import datetime, timedelta
from http.server import BaseHTTPRequestHandler, HTTPServer
import paho.mqtt.client as mqtt

MQTT_HOST = "192.168.30.22"
MQTT_PORT = 1883
MQTT_USER = "mqtt"
MQTT_PASS = "mqtt"

WIIM_IP = "192.168.2.9"
WIIM_UPNP_PORT = 49152
LOCAL_IP = "192.168.2.2"
LOCAL_PORT = 5000

LOG_PATH = "/var/log/mqtt/wiim.log"
RAW_LOG_PATH = "/var/log/mqtt/wiim_raw.log"

ALLOWED_KEYS = {
    "artist", "title", "album", "album_art", "song_id", "subid",
    "rate_hz", "format_depth", "bitrate", "transportstate",
    "reltime", "trackduration", "seconds_elapsed", "seconds_total"
}

mqtt_client = mqtt.Client(client_id="wiim_bridge", callback_api_version=mqtt.CallbackAPIVersion.VERSION2)
mqtt_client.username_pw_set(MQTT_USER, MQTT_PASS)
mqtt_connected = False
avtransport_control_url = None
last_published_state = {}

current_playback_state = {
    "transportstate": "STOPPED",
    "reltime": "00:00:00",
    "trackduration": "00:00:00",
    "seconds_elapsed": 0,
    "seconds_total": 0
}
state_lock = threading.Lock()

def log(*args):
    now = datetime.now()
    timestamp = now.strftime('%d-%m %H:%M:%S') + f".{now.microsecond // 1000:03d}"
    msg = f"{timestamp} " + " ".join(map(str, args))
    print(msg)
    try:
        os.makedirs(os.path.dirname(LOG_PATH), exist_ok=True)
        with open(LOG_PATH, "a", encoding="utf-8") as f:
            f.write(msg + "\n")
    except:
        pass

def log_raw(raw_data):
    try:
        os.makedirs(os.path.dirname(RAW_LOG_PATH), exist_ok=True)
        with open(RAW_LOG_PATH, "a", encoding="utf-8") as f:
            f.write(f"--- {datetime.now()} ---\n{raw_data}\n\n")
    except:
        pass

def on_connect(client, userdata, flags, reason_code, properties):
    global mqtt_connected
    if reason_code == 0:
        mqtt_connected = True
        log("✅ Verbonden met MQTT Broker")

mqtt_client.on_connect = on_connect
mqtt_client.connect(MQTT_HOST, MQTT_PORT, 60)
mqtt_client.loop_start()

def time_to_seconds(t_str):
    try:
        parts = t_str.split(':')
        if len(parts) == 3:
            return int(parts[0]) * 3600 + int(parts[1]) * 60 + int(parts[2])
    except:
        pass
    return 0

def seconds_to_time(sec):
    return str(timedelta(seconds=sec))

def mqtt_publish(subtopic, value, retain=True):
    global last_published_state
    if last_published_state.get(subtopic) == value:
        return
    last_published_state[subtopic] = value
    if mqtt_connected:
        topic = f"wiim/{subtopic}"
        mqtt_client.publish(topic, value, retain=retain, qos=1)
        log(f"📤 MQTT PUBLISH -> wiim/{subtopic}: {value}")

def parse_and_publish_all(data_dict):
    global current_playback_state
    with state_lock:
        for key, val in data_dict.items():
            if val is not None and val != "":
                k_lower = key.lower()
                if k_lower in ALLOWED_KEYS:
                    current_playback_state[k_lower] = val
                    mqtt_publish(k_lower, val)
                
        if "reltime" in data_dict:
            current_playback_state["seconds_elapsed"] = time_to_seconds(data_dict["reltime"])
            mqtt_publish("seconds_elapsed", str(current_playback_state["seconds_elapsed"]))
            
        if "trackduration" in data_dict:
            current_playback_state["seconds_total"] = time_to_seconds(data_dict["trackduration"])
            mqtt_publish("seconds_total", str(current_playback_state["seconds_total"]))
            
        filtered_state = {k: v for k, v in current_playback_state.items() if k in ALLOWED_KEYS}
        payload = json.dumps(filtered_state)
        mqtt_publish("state_json", payload)
    
    artist = data_dict.get("artist", "")
    title = data_dict.get("title", "")
    quality = data_dict.get("quality", "")
    rate = data_dict.get("rate_hz", "")
    depth = data_dict.get("format_depth", "")
    if title or artist:
        log(f"🎶 Artiest: {artist} | Titel: {title} | Album: {data_dict.get('album')} [{quality} {rate}Hz/{depth}bit]")

def parse_didl_metadata(didl_xml, current_data):
    if not didl_xml or didl_xml == "NOT_IMPLEMENTED":
        return current_data
    try:
        clean_meta = html.unescape(didl_xml)
        track_root = ET.fromstring(clean_meta)
        ns = {
            'dc': 'http://purl.org/dc/elements/1.1/',
            'upnp': 'urn:schemas-upnp-org:metadata-1-0/upnp/',
            'song': 'www.wiimu.com/song/'
        }
        
        current_data["title"] = track_root.find('.//dc:title', ns).text if track_root.find('.//dc:title', ns) is not None else current_data.get("title", "")
        current_data["artist"] = track_root.find('.//upnp:artist', ns).text if track_root.find('.//upnp:artist', ns) is not None else current_data.get("artist", "")
        current_data["album"] = track_root.find('.//upnp:album', ns).text if track_root.find('.//upnp:album', ns) is not None else current_data.get("album", "")
        current_data["album_art"] = track_root.find('.//upnp:albumArtURI', ns).text if track_root.find('.//upnp:albumArtURI', ns) is not None else current_data.get("album_art", "")
        
        current_data["subid"] = track_root.find('.//song:subid', ns).text if track_root.find('.//song:subid', ns) is not None else ""
        current_data["song_id"] = track_root.find('.//song:id', ns).text if track_root.find('.//song:id', ns) is not None else ""
        current_data["rate_hz"] = track_root.find('.//song:rate_hz', ns).text if track_root.find('.//song:rate_hz', ns) is not None else ""
        current_data["format_depth"] = track_root.find('.//song:format_s', ns).text if track_root.find('.//song:format_s', ns) is not None else ""
        current_data["bitrate"] = track_root.find('.//song:bitrate', ns).text if track_root.find('.//song:bitrate', ns) is not None else ""
        current_data["quality"] = track_root.find('.//song:actualQuality', ns).text if track_root.find('.//song:actualQuality', ns) is not None else ""
    except Exception as e:
        log(f"⚠️ Fout bij parsen DIDL: {e}")
    return current_data

def process_event_xml(xml_content):
    data = {}
    try:
        root = ET.fromstring(xml_content)
        for elem in root.iter():
            if elem.tag.endswith('LastChange') and elem.text:
                inner_root = ET.fromstring(elem.text)
                for subelem in inner_root.iter():
                    tag_name = subelem.tag.split('}')[-1]
                    val = subelem.attrib.get('val') or subelem.text
                    if val is not None:
                        if tag_name == 'CurrentTrackMetaData':
                            data = parse_didl_metadata(val, data)
                        elif tag_name in ['RelativeTimePosition', 'RelTime']:
                            data['reltime'] = val
                        elif tag_name in ['CurrentTrackDuration', 'TrackDuration']:
                            data['trackduration'] = val
                        else:
                            data[tag_name.lower()] = val
        if data:
            parse_and_publish_all(data)
    except Exception as e:
        log(f"⚠️ Fout bij verwerken XML: {e}")

def fetch_position_info():
    global avtransport_control_url
    if not avtransport_control_url:
        return
    soap_body = '''<?xml version="1.0" encoding="utf-8"?>
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
  <s:Body>
    <u:GetPositionInfo xmlns:u="urn:schemas-upnp-org:service:AVTransport:1">
      <InstanceID>0</InstanceID>
    </u:GetPositionInfo>
  </s:Body>
</s:Envelope>'''
    headers = {
        "Content-Type": 'text/xml; charset="utf-8"',
        "SOAPAction": '"urn:schemas-upnp-org:service:AVTransport:1#GetPositionInfo"'
    }
    try:
        resp = requests.post(avtransport_control_url, data=soap_body, headers=headers, timeout=3)
        if resp.status_code == 200:
            data = {}
            root = ET.fromstring(resp.content)
            for elem in root.iter():
                tag = elem.tag.split('}')[-1]
                if elem.text:
                    val = elem.text.strip()
                    if tag in ['TrackMetaData', 'CurrentTrackMetaData']:
                        data = parse_didl_metadata(val, data)
                    elif tag == 'RelTime':
                        data['reltime'] = val
                    elif tag == 'TrackDuration':
                        data['trackduration'] = val
                    else:
                        data[tag.lower()] = val
            if data:
                parse_and_publish_all(data)
    except Exception as e:
        log(f"⚠️ SOAP fout: {e}")

def playback_ticker():
    while True:
        time.sleep(1)
        with state_lock:
            if current_playback_state.get("transportstate") == "PLAYING":
                sec_elapsed = current_playback_state.get("seconds_elapsed", 0) + 1
                sec_total = current_playback_state.get("seconds_total", 0)
                if sec_total > 0 and sec_elapsed > sec_total:
                    sec_elapsed = sec_total
                current_playback_state["seconds_elapsed"] = sec_elapsed
                current_playback_state["reltime"] = seconds_to_time(sec_elapsed)
                
                mqtt_publish("reltime", current_playback_state["reltime"])
                mqtt_publish("seconds_elapsed", str(sec_elapsed))

class UPnPNotificationHandler(BaseHTTPRequestHandler):
    def do_NOTIFY(self):
        content_length = int(self.headers.get('Content-Length', 0))
        body = self.rfile.read(content_length).decode('utf-8')
        log_raw(body)
        process_event_xml(body)
        self.send_response(200)
        self.end_headers()

    def log_message(self, format, *args):
        return

def run_http_server():
    server_address = ('', LOCAL_PORT)
    httpd = HTTPServer(server_address, UPnPNotificationHandler)
    log(f"🌐 UPnP Event Listener gestart op poort {LOCAL_PORT}")
    httpd.serve_forever()

def discover_wiim_urls():
    global avtransport_control_url
    desc_url = f"http://{WIIM_IP}:{WIIM_UPNP_PORT}/description.xml"
    try:
        resp = requests.get(desc_url, timeout=5)
        if resp.status_code == 200:
            root = ET.fromstring(resp.content)
            for service in root.iter('{urn:schemas-upnp-org:device-1-0}service'):
                service_type = service.find('{urn:schemas-upnp-org:device-1-0}serviceType')
                if service_type is not None and 'AVTransport' in service_type.text:
                    event_sub_url = service.find('{urn:schemas-upnp-org:device-1-0}eventSubURL').text
                    control_url = service.find('{urn:schemas-upnp-org:device-1-0}controlURL').text
                    if not event_sub_url.startswith('http'):
                        event_sub_url = f"http://{WIIM_IP}:{WIIM_UPNP_PORT}/{event_sub_url.lstrip('/')}"
                    if not control_url.startswith('http'):
                        avtransport_control_url = f"http://{WIIM_IP}:{WIIM_UPNP_PORT}/{control_url.lstrip('/')}"
                    else:
                        avtransport_control_url = control_url
                    return event_sub_url
    except Exception as e:
        log(f"⚠️ Fout bij ophalen description.xml: {e}")
    return None

def subscription_loop():
    callback_url = f"http://{LOCAL_IP}:{LOCAL_PORT}/"
    while True:
        sub_url = discover_wiim_urls()
        if not sub_url:
            time.sleep(10)
            continue
        headers = {
            "CALLBACK": f"<{callback_url}>",
            "NT": "upnp:event",
            "TIMEOUT": "Second-300"
        }
        try:
            resp = requests.request("SUBSCRIBE", sub_url, headers=headers, timeout=5)
            if resp.status_code == 200:
                sid = resp.headers.get("SID", "Onbekend")
                log(f"✅ UPnP abonnement gelukt! SID: {sid}")
                fetch_position_info()
                while True:
                    time.sleep(240)
                    requests.request("SUBSCRIBE", sub_url, headers={"SID": sid, "TIMEOUT": "Second-300"}, timeout=5)
        except Exception:
            pass
        time.sleep(10)

if __name__ == "__main__":
    log("🚀 WiiM UPnP to MQTT Bridge gestart")
    threading.Thread(target=run_http_server, daemon=True).start()
    threading.Thread(target=playback_ticker, daemon=True).start()
    try:
        subscription_loop()
    except KeyboardInterrupt:
        log("👋 Gestopt.")