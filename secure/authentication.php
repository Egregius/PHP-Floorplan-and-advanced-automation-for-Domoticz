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
if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    die('No user agent specified');
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')!==false) {
    $udevice='iPhone';
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')!==false) {
    $udevice='iPad';
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh')!==false) {
    $udevice='Mac';
} else {
    $udevice='other';
}
if (substr($_SERVER['REMOTE_ADDR'], 0, 10)=='192.168.2.') {
    $local=true;
} else {
    $local=false;
}
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (isset($_POST['logout'])) {
    if (isset($_POST['username'])) {
        $user=$_POST['username'];
    }
    setcookie($cookie, null, TIME-86400, '/');
    telegram('Home: '.$user.' logged out', true);
    header("Location:/index.php");
    die("Redirecting to:/index.php");
}
if (getenv('HTTP_CLIENT_IP')) {
    $ipaddress=getenv('HTTP_CLIENT_IP');
} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
    $ipaddress=getenv('HTTP_X_FORWARDED_FOR');
} elseif (getenv('HTTP_X_FORWARDED')) {
    $ipaddress=getenv('HTTP_X_FORWARDED');
} elseif (getenv('HTTP_X_REAL_IP')) {
    $ipaddress=getenv('HTTP_X_REAL_IP');
} elseif (getenv('HTTP_FORWARDED_FOR')) {
    $ipaddress=getenv('HTTP_FORWARDED_FOR');
} elseif (getenv('HTTP_FORWARDED')) {
    $ipaddress=getenv('HTTP_FORWARDED');
} elseif (getenv('REMOTE_ADDR')) {
    $ipaddress=getenv('REMOTE_ADDR');
} else {
    $ipaddress='UNKNOWN';
    die("IP ADDRESS UNKNOWN");
}
if (isset($_REQUEST['username'])&&isset($_REQUEST['password'])) {
    $subuser=$_REQUEST['username'];
    $subpass=$_REQUEST['password'];
    if (isset($users[$subuser])) {
        if ($users[$subuser]==$subpass&&strlen($subuser)>=3&&strlen($subuser)<=5&&strlen($subpass)>=3&&strlen($subpass)<=14) {
            echo 'OK';
            lg(print_r($_SERVER, true));
            if ($subuser=='Gast') {
                koekje($subuser, TIME+86400);
            } else {
                koekje($subuser, TIME+31536000);
            }
            telegram('HOME '.$subuser.' logged in.'.PHP_EOL.'IP '.$ipaddress.PHP_EOL.$_SERVER['HTTP_USER_AGENT'], false);
            header("Location:/index.php");
            die("Redirecting to:/index.php");
        } else {
            fail2ban($ipaddress.' FAILED wrong password');
            $msg="HOME Failed login attempt (Wrong password): ";
            if (isset($subuser)) {
                $msg.=PHP_EOL."USER=".$subuser;
            }
            if (isset($subpass)) {
                $msg.=PHP_EOL."PSWD=".$subpass;
            }

            $msg.=PHP_EOL."IP=".$ipaddress;
            if (isset($_SERVER['REQUEST_URI'])) {
                $msg.=PHP_EOL."REQUEST=".$_SERVER['REQUEST_URI'];
            }
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $msg.=PHP_EOL."AGENT=".$_SERVER['HTTP_USER_AGENT'];
            }
            lg($msg);
            telegram($msg, false);
            die('Wrong password!<br>Try again in 10 minutes.<br>After second fail you are blocked for a week!');
        }
    } else {
        fail2ban($ipaddress.' FAILED unknown user');
        $msg="HOME Failed login attempt (Unknown user): ";
        if (isset($subuser)) {
            $msg.="__USER=".$subuser;
        }
        if (isset($subpass)) {
            $msg.="__PSWD=".$subpass;
        }
        $msg.="__IP=".$ipaddress;
        if (isset($_SERVER['REQUEST_URI'])) {
            $msg.=PHP_EOL."REQUEST=".$_SERVER['REQUEST_URI'];
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $msg.=PHP_EOL."AGENT=".$_SERVER['HTTP_USER_AGENT'];
        }
        lg($msg);
        telegram($msg, false);
        die('Unknown user!<br>Try again in 10 minutes.<br>After second fail you are blocked for a week!');
    }
}
if (isset($_COOKIE[$cookie])) {
    $user=$_COOKIE[$cookie];
    if (in_array($user, $homes)) {
        $authenticated=true;
        $home=true;
        $Usleep=80000;
    }
    if ($user=='Tobi') {
        if ($local==false) {
            die('Enkel op wifi');
        }
        if ($d['gcal']['s']==false) {
            die('Enkel tijdens bezoek');
        }

    } elseif ($user=='Gast'&&$local==false) {
        die('Alleen te gebruiken op wifi');
    }
} else {
    echo '
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <meta name="HandheldFriendly" content="true" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="viewport" content="width=device-width,height=device-width, initial-scale=1, user-scalable=yes, minimal-ui" />
        <link rel="icon" type="image/png" href="images/kodi.png"/>
        <link rel="shortcut icon" href="images/kodi.png"/>
        <link rel="apple-touch-startup-image" href="images/kodi.png"/>
        <link rel="apple-touch-icon" href="images/kodi.png"/>
        <title>Inloggen</title>
        <style>
            html{padding:0;margin:0;color:#ccc;font-family:sans-serif;height:100%;}
            body{padding:0;margin:0;background:#000;width:100%;height:100%;background-image:url(\'/images/_firework.jpg\');background-size:contain;background-repeat:no-repeat;background-attachment:fixed;background-position:center bottom;}
            input[type=text]  {height:35px;width:100%;cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;background-color:#444;color:#ccc;display:inline-block;border:0px solid transparent;padding:2px;margin:1px 0px 1px 1px;-webkit-appearance:none;white-space:nowrap;overflow:hidden;}
            input[type=password]{height:35px;width:100%;cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;background-color:#444;color:#ccc;display:inline-block;border:0px solid transparent;padding:2px;margin:1px 0px 1px 1px;-webkit-appearance:none;white-space:nowrap;overflow:hidden;}
            input[type=submit]{height:35px;width:100%;cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;background-color:#444;color:#ccc;display:inline-block;border:0px solid transparent;padding:2px;margin:1px 0px 1px 1px;-webkit-appearance:none;white-space:nowrap;overflow:hidden;}
        </style>
    </head>
	<body>
		<div style="position:fixed;top:10px;left:10px;">
            <form method="POST">
                <table>
                    <tr>
                        <td>
                            <input type="text" name="username" placeholder="Gebruikersnaam" size="50" maxlength="10"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="password" name="password" placeholder="Wachtwoord" size="50" maxlength="20"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" value="Inloggen"/>
                        </td>
                    </tr>
                </table>
            </form>
		</div>
	</body>
</html>';
}