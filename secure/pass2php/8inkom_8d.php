<?php
if ($status=='On') {
	$last=getCache('8inkom8');
	$other=getCache('8inkom');
	if ($last>$d['time']-5&&$other<$d['time']-600) {
		huisthuis();
		resetsecurity();
	}
}