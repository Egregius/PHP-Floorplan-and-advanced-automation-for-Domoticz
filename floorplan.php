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
require 'secure/functionsfloorplan.php';
$_SESSION['referer']='floorplan.php';
require 'secure/authentication.php';
if ($home) {
    createheader('floorplan');
	handlerequest();
    echo '
	<body class="floorplan">
	    <div class="fix" id="clock">
	    	<a href=\'javascript:navigator_Go("floorplan.php");\' id="time">
	    		LOADING
	    		<div class="fix" style="top:0px;left:-266px;width:480px;height:820px;" onclick="javascript:navigator_Go(\'floorplan.php\')">
				</div>
			</a>
		</div>
	    <div id="placeholder"></div>';
}
?>

    </body>
</html>