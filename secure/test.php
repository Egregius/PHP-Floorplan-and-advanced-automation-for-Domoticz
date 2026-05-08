<?php
header('Access-Control-Allow-Origin: *');
echo '<pre>';
$start=microtime(true);
$user='test';
require 'functions.php';
//require '/var/www/authentication.php';
$d=fetchdata();
//$startloop=microtime(true);
//$d['time']=$startloop;
//$db = Database::getInstance();

//hassplaylist('spotify://playlist/EDM - 1');
    
    
musicAssistantPlayPlaylist(
    'EDM - 1',
    '192.168.2.26',
    $matoken
);


function musicAssistantPlayPlaylist(
    string $playlistName,
    string $maHost,
    string $token
): bool {

    $wsUrl = "ws://{$maHost}:8095/ws";

    // WebSocket connectie openen
    $key = base64_encode(random_bytes(16));

    $headers =
        "GET /ws HTTP/1.1\r\n" .
        "Host: {$maHost}:8095\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "Sec-WebSocket-Key: {$key}\r\n" .
        "Sec-WebSocket-Version: 13\r\n\r\n";

    $socket = fsockopen($maHost, 8095, $errno, $errstr, 10);

    if (!$socket) {
        throw new Exception("WebSocket connectie mislukt: $errstr ($errno)");
    }

    fwrite($socket, $headers);

    // Handshake response lezen
    while (!feof($socket)) {
        $line = fgets($socket);

        if (rtrim($line) === '') {
            break;
        }
    }

    // Auth sturen
    $authPayload = [
        'message_id' => 1,
        'command'    => 'auth',
        'args'       => [
            'access_token' => $token
        ]
    ];

    sendWsFrame($socket, json_encode($authPayload));

    // Auth response lezen
    readWsFrame($socket);

    // Default player ophalen
    $payload = [
        'message_id' => 2,
        'command'    => 'players/default'
    ];

    sendWsFrame($socket, json_encode($payload));

    $response = json_decode(readWsFrame($socket), true);

    if (empty($response['result']['player_id'])) {
        throw new Exception("Geen default player gevonden");
    }

    $playerId = $response['result']['player_id'];

    // Playlist zoeken
    $payload = [
        'message_id' => 3,
        'command'    => 'music/playlists'
    ];

    sendWsFrame($socket, json_encode($payload));

    $response = json_decode(readWsFrame($socket), true);

    $playlistId = null;

    foreach ($response['result'] as $playlist) {

        if (
            isset($playlist['name']) &&
            strtolower($playlist['name']) === strtolower($playlistName)
        ) {
            $playlistId = $playlist['item_id'];
            break;
        }
    }

    if (!$playlistId) {
        throw new Exception("Playlist niet gevonden: {$playlistName}");
    }

    // Playlist starten
    $payload = [
        'message_id' => 4,
        'command'    => 'player_queues/play_media',
        'args'       => [
            'queue_id' => $playerId,
            'media'    => [
                'media_type' => 'playlist',
                'item_id'    => $playlistId
            ]
        ]
    ];

    sendWsFrame($socket, json_encode($payload));

    fclose($socket);

    return true;
}


/**
 * WebSocket frame sturen
 */
function sendWsFrame($socket, string $payload): void
{
    $frame = [];

    $frame[0] = 129;

    $length = strlen($payload);

    if ($length <= 125) {
        $frame[1] = $length | 128;
    } elseif ($length <= 65535) {
        $frame[1] = 126 | 128;
        $frame[2] = ($length >> 8) & 255;
        $frame[3] = $length & 255;
    } else {
        throw new Exception('Payload te groot');
    }

    $mask = random_bytes(4);

    $frame = pack('C*', ...$frame) . $mask;

    for ($i = 0; $i < $length; $i++) {
        $frame .= $payload[$i] ^ $mask[$i % 4];
    }

    fwrite($socket, $frame);
}


/**
 * WebSocket frame lezen
 */
function readWsFrame($socket): string
{
    $header = fread($socket, 2);

    if (strlen($header) < 2) {
        return '';
    }

    $bytes = unpack('C2', $header);

    $length = $bytes[2] & 127;

    if ($length === 126) {
        $extended = fread($socket, 2);
        $data = unpack('n', $extended);
        $length = $data[1];
    } elseif ($length === 127) {
        throw new Exception('Gigantische frames niet ondersteund');
    }

    return fread($socket, $length);
}

function getMaQueueStatus() {
    $ch = curl_init();
    // We bevragen de MA server direct op poort 8095
    curl_setopt($ch, CURLOPT_URL, 'http://192.168.2.26:8095/api/players/queue/syncgroup_xpctkjzj');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // MA API heeft vaak geen Bearer token nodig als je lokaal zit, 
    // maar check je MA instellingen als je een 401 krijgt.
    
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    // In de MA Queue objecten zit vaak de 'metadata' van de huidige stream
    // of de naam van de actieve lijst.
    return $data;
}


function hassgetgroep() {
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.26:8123/api/states/media_player.box_living');
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer '.hasstoken()));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$response=curl_exec($ch);
	curl_close($ch);
	return $response;
}
function hassplaylist($playlist) {
    $ch = curl_init();
    $payload = json_encode([
        "entity_id" => "media_player.box_living",
        "media_id" => $playlist,
        "media_type" => "playlist",
        "enqueue" => "replace_next"
    ]);
    curl_setopt($ch, CURLOPT_URL, 'http://192.168.2.26:8123/api/services/music_assistant/play_media');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . hasstoken()
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200) {
        echo "Foutcode: " . $httpCode . " - Respons: " . $data;
    } else {
        echo "Succes!";
    }
}
echo '</pre>';
echo '<hr>Time:'.number_format(((microtime(true)-$start)*1000), 6);
unset(
	$_COOKIE,
	$_ENV,
	$_GET,
	$_POST,
	$_FILES,
	$_SERVER,
	$d,
	$dow,
	$start,
	$telegrambot,
	$cookie,
	$telegramchatid1,
	$telegramchatid2,
	$eendag,
	$googleTTSAPIKey,
	$log,
	$page,
	$udevice,
	$local,
	$user,
	$ipaddress,
	$timediff,
	$domainname,
	$domoticzurl,
	$dbname,
	$dbuser,
	$dbpass,
	$_SESSION,
	$db,
	$nasip,
	$kodiurl,
	$kodiurl2,
	$dsapikey,
	$owappid,
	$openuv,
	$owid,
	$lat,
	$lon,
	$vurl,
	$weekend,
	$memcache,
	$homes,
	$cameratoken,

);
echo '<hr><hr><hr><pre>';print_r(GET_DEFINED_VARS());echo '</pre>';



function Human_kb($bytes,$dec=2) {
	$size=array('kb','Mb','Gb');
	$factor=floor((strlen($bytes)-1)/3);
	return sprintf("%.{$dec}f", $bytes/pow(1000, $factor)).@$size[$factor];
}



/*-------------------------------------------------*/
//require_once 'gcal/google-api-php-client/vendor/autoload.php';
//NL('Druk 6 voor Geert, 7 voor Peter, 8 voor Sandro, 9 voor Gie.');
//FR('Appuyez 6 pour Geert, 7 pour Peter, 8 pour Sandro, 9 pour Guy.');
function NL($sound){
	global $googleTTSAPIKey;
	require_once 'gcal/google-api-php-client/vendor/autoload.php';
	$client=new GuzzleHttp\Client();
	$requestData=['input'=>['text'=>$sound],'voice'=>['languageCode'=>'nl-NL','name'=>'nl-NL-Wavenet-B'],'audioConfig'=>['audioEncoding'=>'LINEAR16','pitch'=>0.00,'speakingRate'=>0.92,'effectsProfileId' => 'telephony-class-application']];
	try {
		$response=$client->request('POST', 'https://texttospeech.googleapis.com/v1beta1/text:synthesize?key='.$googleTTSAPIKey, ['json'=>$requestData]);
		$fileData=json_decode($response->getBody()->getContents(), true);
		$audio=base64_decode($fileData['audioContent']);
		if(strlen($audio)>10) {
			file_put_contents('/var/www/html/3CX/'.$sound.'.wav', $audio);
		}
	} catch (Exception $e) {
		exit('Something went wrong: ' . $e->getMessage());
	}
}
function FR($sound){
	global $googleTTSAPIKey;
	require_once 'gcal/google-api-php-client/vendor/autoload.php';
	$client=new GuzzleHttp\Client();
	$requestData=['input'=>['text'=>$sound],'voice'=>['languageCode'=>'fr-FR','name'=>'fr-FR-Wavenet-B'],'audioConfig'=>['audioEncoding'=>'LINEAR16','pitch'=>0.00,'speakingRate'=>0.92,'effectsProfileId' => 'telephony-class-application']];
	try {
		$response=$client->request('POST', 'https://texttospeech.googleapis.com/v1beta1/text:synthesize?key='.$googleTTSAPIKey, ['json'=>$requestData]);
		$fileData=json_decode($response->getBody()->getContents(), true);
		$audio=base64_decode($fileData['audioContent']);
		if(strlen($audio)>10) {
			file_put_contents('/var/www/html/3CX/'.$sound.'.wav', $audio);
		}
	} catch (Exception $e) {
		exit('Something went wrong: ' . $e->getMessage());
	}
}
/*---------------------------*/
