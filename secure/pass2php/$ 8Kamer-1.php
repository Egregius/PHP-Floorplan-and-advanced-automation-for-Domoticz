<?php
if ($d['kamer']['m']==2) {
	sl('kamer', (1+$d['kamer']['s']), basename(__FILE__).':'.__LINE__);
	$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.103:8090/volume'))), true);
	bosevolume((1+$volume['actualvolume']), 103);
} elseif ($status=='On') {
	$item='RkamerL';
	if ($d[$item]['s']>0) {
		sl($item, 0, basename(__FILE__).':'.__LINE__);
	}
}
