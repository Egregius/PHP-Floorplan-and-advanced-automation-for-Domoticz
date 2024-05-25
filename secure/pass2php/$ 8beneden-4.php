<?php
if ($status=='On') {
	if ($d['langekast']['s']=='Off') sw('langekast', 'On', basename(__FILE__).':'.__LINE__);
	sw('lamp kast', 'Toggle', basename(__FILE__).':'.__LINE__);
}
