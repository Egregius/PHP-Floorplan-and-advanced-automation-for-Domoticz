<?php
if($status>80)$status=30;
if ($d['bose105']['s']=='Off'&&$status>0) {
	bosezone(105, true, $status);
	sw('bose105', 'On',basename(__FILE__).':'.__LINE__);
} elseif ($d['bose105']['s']=='On'&&$status==0) {
	bosekey("POWER", 0, 105);
	sw('bose105', 'Off',basename(__FILE__).':'.__LINE__);
} else bosevolume($status, 105, basename(__FILE__).':'.__LINE__);
