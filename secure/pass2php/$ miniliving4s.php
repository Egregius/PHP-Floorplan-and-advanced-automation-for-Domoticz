<?php
if($d['sony']['s']=='On') {
	if (past('$ miniliving4s')<=1) fvolume(+4);
	else fvolume(+1);
} else fvolume('up');
store('Weg', 0, basename(__FILE__).':'.__LINE__);