<?php
if($d['sony']['s']=='On') {
	if (past('$ miniliving3s')<=1) fvolume(-4);
	else fvolume(-1);
} else fvolume('down');
store('Weg', 0, basename(__FILE__).':'.__LINE__);