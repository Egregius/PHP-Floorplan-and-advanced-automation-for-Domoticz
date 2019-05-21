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
	    <div id="placeholder"></div>
	    <div id="ip"></div>';
	sidebar();
    dimmer('tobi','i60');
    dimmer('zithoek');
    dimmer('eettafel');
    dimmer('kamer','i60');
    dimmer('alex','i60');
    dimmer('lichtbadkamer','i60');
    dimmer('terras','i48');
    blinds('zoldertrap');
    showTimestamp('belknop', 270);
    showTimestamp('pirgarage', 0);
    showTimestamp('pirliving', 0);
    showTimestamp('pirkeuken', 0);
    showTimestamp('pirinkom', 0);
    showTimestamp('pirhall', 0);
    showTimestamp('achterdeur', 270);
    showTimestamp('poort', 90);
    showTimestamp('raamliving', 270);
    showTimestamp('raamtobi', 270);
    showTimestamp('raamalex', 270);
    showTimestamp('raamkamer', 90);
    showTimestamp('deurbadkamer', 90);
    showTimestamp('deurinkom', 90);
    showTimestamp('deurgarage', 0);
    bose(101);
    bose(102);
    bose(103);
    bose(104);
    thermometer('living_temp');
    thermometer('badkamer_temp');
    thermometer('kamer_temp');
    thermometer('tobi_temp');
    thermometer('alex_temp');
    thermometer('zolder_temp');


}
?>

    </body>
</html>