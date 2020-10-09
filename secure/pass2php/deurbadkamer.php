<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='Open'&&$d['auto']['s']=='On') {
    fbadkamer();
    fhall();
    if ($d['kamer']['m']!=0&&$d['kamer']['s']==0&&past('kamer')<90) {
		storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
	}
} else {
    if (past('$ 8badkamer-8')>10&&$d['lichtbadkamer']['s']==0) {
        if($d['zon']['s']==0||(TIME>strtotime('5:00')&& TIME<strtotime('7:30'))) $d['lichtbadkamer']['s']=25;
        $d['deurbadkamer']['s']='Closed';
        $d['$ 8badkamer-7']['t']=0;
        $d['badkamervuur1']['t']=0;
        $d['badkamervuur2']['t']=0;
        if ($d['heating']['s']==0) include ('_TC_neutral.php');
	elseif ($d['heating']['s']==1) include ('_TC_aircoheating.php');
	elseif ($d['heating']['s']==2) include ('_TC_gasheating.php');
    }
    	
}
if ($status=='Open') sirene('Deur badkamer open');
else sirene('Deur badkamer dicht');