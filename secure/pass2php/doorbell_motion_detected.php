<?php
if ($status=='On'&&$d['auto']=='On') {
	if ($d['dag']['s']<0&&$d['voordeur']['s']=='Off') sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
}