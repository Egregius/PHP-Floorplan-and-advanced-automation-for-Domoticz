<?php
if($status>60)$status=30;
if ($d['bose105']['s']=='Off'&&$status>0) {
	sw('bose105', 'On');
	bosezone(105, true);
} elseif ($d['bose105']['s']=='On'&&$status==0) {
	sw('bose105', 'Off');
	bosekey("POWER", 75000, 105);
} else bosevolume($status, 105, basename(__FILE__).':'.__LINE__);
