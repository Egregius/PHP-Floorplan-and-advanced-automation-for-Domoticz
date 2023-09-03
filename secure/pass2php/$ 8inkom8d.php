<?php
$last=mget('8inkom');
if ($last>time()-5) {
	huisthuis();
	resetsecurity();
}
