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

if($status=='Off') {
    if($d['pirkeuken']['s']!='Off'&&$d['kookplaat']['s']=='Off'&&$d['wasbak']['s']=='Off') {
        store('pirkeuken', 'Off', basename(__FILE__).':'.__LINE__);
    }
    if ($d['kookplaat']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['GroheRed']['m']==1) {
    	storemode('GroheRed', 0, basename(__FILE__).':'.__LINE__);
    }
    if ($d['wasbak']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['GroheRed']['s']=='On') {
    	sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
    }
}