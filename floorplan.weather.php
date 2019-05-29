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
    $ds=json_decode(file_get_contents('/temp/ds.json'));
    unset($ds->minutely);
	$ow=json_decode(file_get_contents('/temp/ow.json'));
	echo '
	<body>
	    <div class="fix" id="clock"><a href=\'javascript:navigator_Go("floorplan.weather.php");\' id="time">Refresh</a></div>
	    <div>
	        <br>
	        <table>
	            <thead>
    	            <tr>
    	                <th></th>
    	                <th></th>
    	                <th>Temp</th>
    	                <th></th>
    	            </tr>
    	        </thead>
    	        <tbody>
    	            <tr>
    	                <td>Nu:</td>
    	                <td>'.$ds->currently->summary.'</td>
    	                <td>'.number_format($ds->currently->temperature, 1, ',', '').'&#8451;</td>
    	            </tr>
    	        </tbody>
	        </table>
	        Komende 48u:
	        <table>
	            <thead>
    	            <tr>
    	                <th></th>
    	                <th></th>
    	                <th>Temp</th>
    	                <th></th>
    	            </tr>
    	        </thead>
    	        <tbody>';
    foreach ($ds->hourly->data as $i) {
        echo '
    	            <tr>
    	                <td>'.$i->time.'</td>
    	                <td>'.$i->summary.'</td>
    	                <td>'.number_format($i->temperature, 1, ',', '').'&#8451;</td>
    	            </tr>';
    }
    echo '
    	        </tbody>
    	    </table>

	    </div>';
	echo '<pre>';print_r($ds);echo '</pre>';
	echo '<pre>';print_r($ow);echo '</pre>';
	echo '<script>setTimeout("window.location.href=window.location.href;", 1000);</script>';
}