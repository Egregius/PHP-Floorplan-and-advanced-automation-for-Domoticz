<?php
if ($status=='On'&&$d['deurvoordeur']->s=='Closed') {
	if ($d['dag']->s<0) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	if ($time>getCache('ring_ding')+30) {
		if ($d['deurvoordeur']->s=='Closed') {
			setCache('ring_ding', $time);
			if ($d['weg']->s==0) {
				if (($d['bose102']->s=='On'||$d['bose103']->s=='On'||$d['bose104']->s=='On'||$d['bose105']->s=='On'||$d['bose106']->s=='On'||$d['bose107']->s=='On'||$d['bose108']->s=='On'||$d['bose109']->s=='On')&&$d['pirliving']->s=='Off'&&$d['pirkeuken']->s=='Off') {
					shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose101.php" > /dev/null 2>/dev/null &');
				}

			}
		}
	}
}