<?php
if ($status=='On'&&$d['auto']=='On') {
	if ($d['dag']['s']<2&&$d['voordeur']['s']=='Off') sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
}