<?php
if ($status=='On') {
	if ($d['langekast']['s']=='Off') sw('langekast', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($d['langekast']['s']=='On') {
		if ($d['lamp kast']['s']=='Off') sw('lamp kast', 'On', basename(__FILE__).':'.__LINE__);
		elseif ($d['lamp kast']['s']=='On') sw('lamp kast', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
