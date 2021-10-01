<?php
if ($d['Weg']['s']==0) {
	if ($d['daikinliving']['m']==3||$d['daikinkamer']['m']==3||$d['daikinalex']['m']==3) {$rgb=230;$mode=3;}
	elseif ($d['daikinliving']['m']==4||$d['daikinkamer']['m']==4||$d['daikinalex']['m']==4) {$rgb=1;$mode=4;}
	elseif ($d['daikinliving']['m']==2||$d['daikinkamer']['m']==2||$d['daikinalex']['m']==2) {$rgb=56;$mode=2;}
	else $rgb=false;
	if ($rgb!=false) {
		$level=explode(';', $status);
		$level=$level[0];
		$level=round($level/30);
		if ($level>100) $level=100;
		elseif ($level<1) $level=1;
		if ($d['Xlight']['s']!=$level) {
			rgb('Xlight', $rgb, $level);
			sl('Xlight', $level, basename(__FILE__).':'.__LINE__);
		}
	} else {
		if ($d['Xlight']['s']>0) sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);
	}
} else {
	if ($d['Xlight']['s']>0) sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);
}
