#!/usr/bin/php
<?php
$lock_file = fopen('/run/lock/'.basename(__FILE__).'.pid', 'c');
$got_lock = flock($lock_file, LOCK_EX | LOCK_NB, $wouldblock);
if ($lock_file === false || (!$got_lock && !$wouldblock)) {
    throw new Exception("Unexpected error opening or locking lock file.");
} else if (!$got_lock && $wouldblock) {
    exit("Another instance is already running; terminating.\n");
}
pcntl_async_signals(true);

// Definieer wat er moet gebeuren bij een stop-signaal
$saveAndExit = function ($signo) use (&$history) {
    lg("🛑 Signaal $signo ontvangen. Geschiedenis opslaan en afsluiten...", 'cron2');
    if (!empty($history)) {
        file_put_contents('/var/www/spotifyhistory.json', json_encode($history));
    }
    exit;
};

// Luister naar SIGTERM (reboot/systemctl stop) en SIGINT (CTRL+C)
pcntl_signal(SIGTERM, $saveAndExit);
pcntl_signal(SIGINT, $saveAndExit);

require '/var/www/html/secure/functions.php';
lg('🟢 Starting cron2 loop...','cron2');
$time=time();
$d=fetchdata();
$lastcheck=$time;
$lastping=$time;
define('LOOP_START', $time);
$invalidcounter=0;
$history = file_exists('/var/www/spotifyhistory.json') ? json_decode(file_get_contents('/var/www/spotifyhistory.json'), true) : [];
$prevtrackid = !empty($history) ? array_key_last($history) : null;

$ctx=stream_context_create(array('http'=>array('timeout' =>1.5)));
$devices = [
    101 => 14,
    102 => 22,
    103 => 32,
    104 => 32,
    105 => 32,
    106 => 22,
    107 => 32,
];
$user='cron2B';
$boses=array(
	101=>'Living',
	102=>'102',
	103=>'Boven',
	104=>'Garage',
	105=>'10-Wit',
	106=>'Buiten20',
	107=>'Keuken',
);
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require '/var/www/vendor/autoload.php';
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt')
	->setKeepAliveInterval(60);
$mqtt=new MqttClient('192.168.30.22',1883,basename(__FILE__),MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);

while (1) {
    $time = time();
    $d['time'] = $time;
    $d = fetchdata();
    include 'cron2B.php';
    $time_elapsed_secs = microtime(true) - $time;
    $sleep = 10 - $time_elapsed_secs;
    if ($sleep > 0) {
        $sleep = round($sleep * 1000000);
        usleep($sleep);
    }
    if ($lastping < $time - 60) {
        $lastping = $time;
        $mqtt->publish('p', 'p');
    }
    if ($lastcheck < $time - 300) {
        $lastcheck = $time;
        stoploop();
        $d=fetchdata(0);
    }
}

function stoploop() {
    global $lock_file;
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('🛑 functions.php gewijzigd → restarting cron2 loop...','cron2');
        if (!empty($history)) file_put_contents('/var/www/spotifyhistory.json', json_encode($history));
        ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 5 /usr/bin/php8.2 $script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime(__DIR__ . '/cron2.php') > LOOP_START) {
        lg('🛑 cron2.php gewijzigd → restarting cron2 loop...','cron2');
        if (!empty($history)) file_put_contents('/var/www/spotifyhistory.json', json_encode($history));
        ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 5 /usr/bin/php8.2 $script > /dev/null 2>&1 &");
        exit;
    }
}
function cleanTitle(string $artists, string $title): string
{
	static $replace = null;

	if ($replace === null) {
		$replace = [
			'albummix','clubedit','clubmix','edit','extended',
			'feat','ft','featuring',
			'festivalmix','mixedit','originalmix','original',
			'radio','radioedit','radiomix','radioversion',
			'remastered','remaster',
			'remix','rework','mix',
			'singleversion','version',
			'videoedit','7"',
		];
	}

	$artists = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $artists));
	$title   = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title));

	$title = preg_replace('/\b(19|20)\d{2}\b/', '', $title);

	$arr = array_map('trim', explode(',', $artists));
	sort($arr);

	$str = implode('', $arr) . $title;

	$str = preg_replace('/\b(' . implode('|', array_map('preg_quote', $replace)) . ')\b/', '', $str);

	$str = preg_replace('/[^a-z0-9]/', '', $str);

	return $str;
}