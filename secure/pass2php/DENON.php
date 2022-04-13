<?php
if($status>45)$status=25;
if ($d['denon']['s']=='On'&&$status>0&&$status<=45) {
	$status=80-$status;
	@file_get_contents('http://192.168.2.5/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.number_format($status, 0).'.0');
}
