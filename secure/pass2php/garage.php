<?php
if ($status=='Off') {
	if ($d['Weg']['s']==0&&($d['zon']['s']<100||TIME<strtotime('9:00')||TIME>strtotime('21:00'))&&$d['garageled']['s']=='Off') {
		sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
	}
} elseif ($status=='On') {
	if ($d['garageled']['s']=='On') {
		sw('garageled','Off', basename(__FILE__).':'.__LINE__);
	}
}
