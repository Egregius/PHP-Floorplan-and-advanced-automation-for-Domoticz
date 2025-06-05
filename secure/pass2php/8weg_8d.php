<?php
if ($status=='On') {
	$last=mget('8weg');
	if ($last>time()-5) {
		huisthuis();
		resetsecurity();
	}
}