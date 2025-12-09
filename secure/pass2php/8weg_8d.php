<?php
if ($status=='On') {
	$last=getCache('8weg8');
	$other=getCache('8weg');
	if ($last>$d['time']-5&&$other<$d['time']-600) {
		huisthuis();
		resetsecurity();
	}
}