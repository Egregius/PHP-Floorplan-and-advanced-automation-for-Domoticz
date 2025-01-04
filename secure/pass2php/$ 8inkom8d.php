<?php
$last=mget('8inkom8');
$other=mget('8inkom');
$time=time();
if ($last>$time-5&&$other<$time-600) {
	huisthuis();
	resetsecurity();
}
