<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * This is the main floorplan.
 * It handles all the lighting and shows status of heating and rollers.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
$_SESSION['referer']='floorplan.php';
require 'secure/authentication.php';
if ($home==true) {
    createheader('floorplan');
    echo '
	<body class="floorplan">
	    <div class="fix" id="clock">
	    	<a href=\'javascript:location.reload(true);\' id="time">
	    		Loading...
			</a>
		</div>
	    <div id="placeholder">
	    	<div class="fix z" style="top:0px;left:-285px;width:480px;height:820px;backgound-color:#F00;" onclick="javascript:location.reload(true)">
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				Push to refresh...<br>
				<br>
				<a href="/index.php">Menu</a>
			</div>
		</div>';
}
?>

    </body>
</html>