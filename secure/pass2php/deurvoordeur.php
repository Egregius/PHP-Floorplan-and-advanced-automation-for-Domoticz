<?php
if ($status=="Open"&&$d['auto']['s']=='On') {
	if ($d['voordeur']['s']=='Off'&&$d['dag']==0) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($d['voordeur']['s']=='On'&&$d['zon']>0) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
	finkom();
	if (mget('ring_ding')>$d['time']-300) {
		if ($d['bureel']['s']!='Off') sw('bureel', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['lamp kast']['s']!='Off') sw('lamp kast', 'Off', basename(__FILE__).':'.__LINE__);
	}
} else {
	if ($d['weg']['s']==0) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
}
/*if ($status=='Open') sirene('Voordeur open');
else sirene('Voordeur dicht');*/

