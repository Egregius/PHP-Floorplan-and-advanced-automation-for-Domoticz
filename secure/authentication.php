<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$authenticated=false;
$home=false;
if (!isset($_SERVER['HTTP_USER_AGENT'])) die('No user agent specified');
elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh')!==false) $udevice='Mac';
elseif (strpos($_SERVER['HTTP_USER_AGENT'], '10_15')!==false) $udevice='iPhone';
elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')!==false) $udevice='iPhone';
elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')!==false) $udevice='iPad';
else $udevice='other';
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ipaddress=$_SERVER['HTTP_X_FORWARDED_FOR'];
	$local=true;
} else {
	$ipaddress=$_SERVER['REMOTE_ADDR'];
	$local=false;
}

//header("Expires: on, 01 Jan 1970 00:00:00 GMT");
//header("Last-Modified: Tue, 10 Dec 2025 14:50:27 GMT");
//header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
//header("Cache-Control: post-check=0, pre-check=0", false);
//header("Pragma: no-cache");
if (isset($_POST['logout'])) {
	$user='';
    if (isset($_POST['username'])) {
        $user=$_POST['username'];
    }
    setcookie($cookie, $user, TIME-86400, '/', $domainname, true, true);
    telegram('Home: '.$user.' logged out', true);
    header("Location:/index.php");
    die("Redirecting to:/index.php");
}

if (isset($_POST['username'])&&isset($_POST['password'])) {
    if (isset($users[$_POST['username']])) {
        if ($users[$_POST['username']]==$_POST['password']) {
            echo 'OK';
            lg(print_r($_SERVER, true));
            koekje($_POST['username'], 2147483647);
            telegram('HOME '.$_POST['username'].' logged in.'.PHP_EOL.'IP '.$_SERVER['REMOTE_ADDR'].PHP_EOL.$_SERVER['HTTP_USER_AGENT'], false);
            sleep(2);
            if (!empty($_SESSION['referer'])) {
                header("Location:/".$_SESSION['referer']);
                die("Redirecting to:/".$_SESSION['referer']);
            } else {
                header("Location:/index.php");
                die("Redirecting to:/index.php");
            }
        } else {
            fail2ban($_SERVER['REMOTE_ADDR'].' FAILED wrong password');
            $msg="HOME Failed login attempt (Wrong password): ";
            if (isset($_POST['username'])) {
                $msg.=PHP_EOL."USER=".$_POST['username'];
            }
            if (isset($_POST['password'])) {
                $msg.=PHP_EOL."PSWD=".$_POST['password'];
            }

            $msg.=PHP_EOL."IP=".$_SERVER['REMOTE_ADDR'];
            if (isset($_SERVER['REQUEST_URI'])) {
                $msg.=PHP_EOL."REQUEST=".$_SERVER['REQUEST_URI'];
            }
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $msg.=PHP_EOL."AGENT=".$_SERVER['HTTP_USER_AGENT'];
            }
            lg($msg);
            telegram($msg, false);
            die('<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <meta name="HandheldFriendly" content="true" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="viewport" content="width=device-width,height=device-width, initial-scale=1, user-scalable=yes, minimal-ui" />
        <link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
        <link rel="shortcut icon" href="images/domoticzphp48.png"/>
        <link rel="apple-touch-startup-image" href="images/domoticzphp144.png"/>
        <link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
        <script type="text/javascript" src="/scripts/jQuery.js"></script>
		<script type="text/javascript" src="/scripts/floorplanjs.js"></script>
		<title>Inloggen</title>
    </head>
	<body>
		<div style="position:fixed;top:10px;left:10px;" onclick="javascript:navigator_Go(\'floorplan.php\')">
			Wrong password!<br>
			Try again in 10 minutes.<br>
			After second fail you are blocked for a week!
		</div>
	</body>
</html>');
        }
    } else {
        fail2ban($_SERVER['REMOTE_ADDR'].' FAILED unknown user');
        $msg="HOME Failed login attempt (Unknown user): ";
        if (isset($_POST['username'])) {
            $msg.="__USER=".$_POST['username'];
        }
        if (isset($_REQ_POSTEST['password'])) {
            $msg.="__PSWD=".$_POST['password'];
        }
        $msg.="__IP=".$_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['REQUEST_URI'])) {
            $msg.=PHP_EOL."REQUEST=".$_SERVER['REQUEST_URI'];
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $msg.=PHP_EOL."AGENT=".$_SERVER['HTTP_USER_AGENT'];
        }
        lg($msg);
        telegram($msg, false);
        die('<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <meta name="HandheldFriendly" content="true" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="viewport" content="width=device-width,height=device-width, initial-scale=1, user-scalable=yes, minimal-ui" />
        <link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
        <link rel="shortcut icon" href="images/domoticzphp48.png"/>
        <link rel="apple-touch-startup-image" href="images/domoticzphp144.png"/>
        <link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
        <script type="text/javascript" src="/scripts/jQuery.js"></script>
		<script type="text/javascript" src="/scripts/floorplanjs.js"></script>
		<title>Inloggen</title>
    </head>
	<body>
		<div style="position:fixed;top:10px;left:10px;" onclick="javascript:navigator_Go(\'floorplan.php\')">
			Unknown user!<br>
			Try again in 10 minutes.<br>
			After second fail you are blocked for a week!
		</div>
	</body>
</html>');
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
        $d=fetchdata();
        if ($local==false) {
            die('Enkel op wifi');
        }
        if ($d['gcal']['s']==false) {
            die('Enkel tijdens bezoek');
        }

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
        <link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
        <link rel="shortcut icon" href="images/domoticzphp48.png"/>
        <link rel="apple-touch-startup-image" href="images/domoticzphp144.png"/>
        <link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
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
                            <input type="password" name="password" placeholder="Wachtwoord" size="50" maxlength="200"/>
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
            <button class="btn" onclick="javascript:navigator_Go(\'index.php\');">Reload</button>
		</div>
	</body>
</html>';
}