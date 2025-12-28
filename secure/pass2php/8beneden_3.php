<?php
if ($status=='On') {
	sw('media', 'On', basename(__FILE__).':'.__LINE__, true);
	sw('zetel', 'On', basename(__FILE__).':'.__LINE__, true);
	if ($d['rbureel']['s']<100&&$d['time']>=strtotime('18:00')) sl('rbureel', 100, basename(__FILE__).':'.__LINE__, true);
}