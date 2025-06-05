<?php
if ($status=='On') {
	$last=mget('8inkom8');
	$other=mget('8inkom');
	if ($last>$d['time']-5&&$other<$d['time']-600) {
		huisthuis();
		resetsecurity();
	}
}