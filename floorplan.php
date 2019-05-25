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
require 'secure/authentication.php';
if ($home) {
    createheader('floorplan');
	handlerequest();
    echo '
	<body class="floorplan">
	    <div id="placeholder"></div>';
	sidebar();
    dimmer('tobi','i60');
    dimmer('zithoek');
    dimmer('eettafel');
    dimmer('kamer','i60');
    dimmer('alex','i60');
    dimmer('lichtbadkamer','i60');
    dimmer('terras','i48');
    blinds('zoldertrap');
    bose(101);
    bose(102);
    bose(103);
    bose(104);
}
?>

    </body>
</html>