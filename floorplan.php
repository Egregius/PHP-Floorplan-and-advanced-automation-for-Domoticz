<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
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
if ($home) {
    createheader('floorplan');
    echo '
	<body class="floorplan">
	    <div class="fix" id="clock">
	    	<a href=\'javascript:location.reload(true);\' id="time">
	    		Loading...
	    		<div class="fix" style="top:0px;left:-276px;width:480px;height:820px;" onclick="javascript:location.reload(true)">
	    			<br>
		    		<br>
		    		<br>
		    		Push to refresh...<br>
				</div>
			</a>
		</div>
	    <div id="placeholder"></div>';
}
?>

    </body>
</html>