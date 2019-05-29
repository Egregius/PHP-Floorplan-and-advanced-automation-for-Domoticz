<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * This is the weather floorplan.
 * It shows the information stored from darksky and openweathermap.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
require 'secure/functionsfloorplan.php';
require 'secure/authentication.php';
if ($home) {
    createheader();
    echo '
	<body>
	    <div class="fix" id="clock"><a href=\'javascript:navigator_Go("floorplan.weather.php");\' id="time">Refresh</a></div>
	    <div id="placeholder"></div>';
}