<?php
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
if ($d['auto']['s']=='On') {
	fhall();
	finkom();
	fliving();
}
if ($d['Weg']['s']!=0) {
    store('Weg', 0, basename(__FILE__).':'.__LINE__);
}
resetsecurity();