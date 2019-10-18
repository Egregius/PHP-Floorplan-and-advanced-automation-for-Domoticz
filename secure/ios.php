<?PHP
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
require '/var/www/config.php';
if (isset($_REQUEST['text'])) {
    for ($k=1;$k<=5;$k++) {
        include_once 'findmyiphone.php';
        $fmi=new FindMyiPhone($appleid, $applepass);
        $reply=$fmi->playSound($appledevice, $_REQUEST['text']);
        echo $reply.PHP_EOL;
        if ($reply=='') {
            die('OK');
        } else {
        	echo 'reply = '.$reply.'<br>';
        }
        sleep($k);
    }
}