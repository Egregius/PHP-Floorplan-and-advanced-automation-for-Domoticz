<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$smappee=json_decode(
    file_get_contents(
        'http://192.168.2.15/gateway/apipublic/reportInstantaneousValues'
    ),
    true
);
//lg('        ___SMAPPEE___');
if (!empty($smappee['report'])) {
    preg_match_all(
        "/ activePower=(\\d*.\\d*)/",
        $smappee['report'],
        $matches
    );
    if (!empty($matches[1][1])) {
        $time=time();
        $db=new PDO(
            "mysql:host=localhost;dbname=domotica;",
            'domotica',
            'domotica'
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $newzon=round($matches[1][1], 0);
        if ($newzon<0) {
            $newzon=0;
        }
        if ($newzon==0 ) {
            $result = $db->query(
                "UPDATE devices SET s='$newzon' WHERE n='zon';"
            ) or trigger_error($db->error);
        } else {
            $result = $db->query(
                "UPDATE devices SET s='$newzon',t='$time' WHERE n='zon';"
            ) or trigger_error($db->error);
        }
        if (!empty($matches[1][2])) {
            $consumption=round($matches[1][2], 0);
            $result = $db->query(
                "UPDATE devices SET s='$consumption',t='$time' WHERE n='el';"
            ) or trigger_error($db->error);
            if ($consumption>8000) {
                alert('Power', 'Power usage: '.$consumption.' W!', 120, false);
            }
        }
    }
} else {
    if (shell_exec('curl -H "Content-Type: application/json" -X POST -d "" http://192.168.2.15/gateway/apipublic/logon')!='{"success":"Logon successful!","header":"Logon to the monitor portal successful..."}') {
        exit;
    }
}
/**
 * Function telegram
 * Sends a message to telegram
 *
 * @param string  $msg    Message to sent
 * @param boolean $silent If true deactivates the notification sound
 * @param int     $to     send to 1 or 2 chats
 *
 * @return null
 */
function telegram($msg,$silent=true,$to=1)
{
    $msg=str_replace('__', PHP_EOL, $msg);
    shell_exec(
        './telegram.sh "'.$msg.'" "'.$silent.'" "'.$to.'" > /dev/null 2>/dev/null &'
    );
}
/**
 * Function alert
 * Sends a message to telegram but checks when last message is sent
 *
 * @param string  $name   id of the alert
 * @param string  $msg    Message to sent
 * @param int     $ttl    minimum seconds between 2 alerts
 * @param boolean $silent If true deactivates the notification sound
 * @param boolean $ios    If true also send to iOS
 *
 * @return null
 */
function alert($name,$msg,$ttl,$silent=true,$ios=false)
{
    global $db;
    if ($ios) {
        shell_exec('./ios.sh "'.$msg.'" > /dev/null 2>/dev/null &');
    }
    $time=time();
    $stmt=$db->query("SELECT t FROM alerts WHERE n='$name';");
    $last=$stmt->fetch(PDO::FETCH_ASSOC);
    if (isset($last['t'])) {
        $last=$last['t'];
    }
    if ($last < $time-$ttl) {
        telegram($msg, $silent);
        $db->query(
            "INSERT INTO alerts (n,t) VALUES ('$name','$time')
            ON DUPLICATE KEY UPDATE t='$time';"
        );
    }
}
function lg($msg)
{
    $fp=fopen('/var/log/domoticz.log', "a+");
    $time=microtime(true);
    $dFormat="Y-m-d H:i:s";
    $mSecs=$time-floor($time);
    $mSecs=substr(number_format($mSecs, 3), 1);
    fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
    fclose($fp);
}