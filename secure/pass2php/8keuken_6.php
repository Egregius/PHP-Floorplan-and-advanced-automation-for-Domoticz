<?php
if ($status=='On') {
	if ($d['snijplank']->s==0) {
		zwave('snijplank','multilevel',0,25);
	} else zwave('snijplank','multilevel',0,0);
}
