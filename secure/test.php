<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$start=microtime(true);
require 'functions.php';
echo '<pre>';
if(!ob_get_level()) ob_start();
/*-------------------------------------------------*/
/*for($y=1;$y<=1;$y++) {
	for ($x=10;$x<=30;$x=$x+1) {
		if(!file_exists('/var/www/html/sounds/douche-'.$y.$x.'.mp3')) {
			echo 'fetching '.$y.$x.'<br>';
			$postdata = http_build_query(
					array(
						'msg' => 'Douche. '.$y.' euro '.$x.' cent.',
						'lang' => 'Ruben',
						'source' => 'ttsmp3'
					)
				);

				$opts = array('http' =>
					array(
						'method'  => 'POST',
						'header'  => 'Content-Type: application/x-www-form-urlencoded',
						'content' => $postdata
					)
				);

				$context  = stream_context_create($opts);

				$result = json_decode(file_get_contents('https://ttsmp3.com/makemp3.php', false, $context), true);
				print_r($result);
				if($result['Error']==0) {
					$mp3=file_get_contents($result['URL']);
					if(strlen($mp3)>1000) {
						file_put_contents('/var/www/html/sounds/douche-'.$y.$x.'.mp3', $mp3);
					}
				} else {
					exit;
				}
				ob_end_flush();
				ob_flush();
				flush();
			}
	}
}*/
douche();


/*---------------------------*/
echo '</pre>';
$total=microtime(true)-$start;
echo '<hr>Time:'.number_format(((microtime(true)-$start)*1000), 6);
unset(
    $_COOKIE,
    $_GET,
    $_POST,
    $_FILES,
    $_SERVER,
    $start,
    $total,
    $users,
    $homes,
    $telegrambot,
    $smappeeclient_id,
    $smappeeclient_secret,
    $smappeeusername,
    $smappeepassword,
    $smappeeserviceLocationId,
    $LogFile,
    $cookie,
    $telegramchatid,
    $telegramchatid2,
    $smappeeip,
    $authenticated,
    $zongarage,
    $zonkeuken,
    $zoninkom,
    $zonmedia,
    $Usleep,
    $eendag,
    $Weg,
    $home,
    $log,
    $offline,
    $page,
    $udevice,
    $local,
    $user,
    $ipaddress,
    $timediff
);
echo '<hr><hr><hr><pre>';print_r(GET_DEFINED_VARS());echo '</pre>';



function Human_kb($bytes,$dec=2)
{
    $size=array('kb','Mb','Gb');
    $factor=floor((strlen($bytes)-1)/3);
    return sprintf("%.{$dec}f", $bytes/pow(1000, $factor)).@$size[$factor];
}

function Get_data($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.17 Safari/537.36');
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
