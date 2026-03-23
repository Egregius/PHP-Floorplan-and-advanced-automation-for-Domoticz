<?php
if ($status=="Open") {
	setCache('timestampweg',$time+300);
	if ($d['auto']->s=='On') {
		if(&&$d['voordeur']->s=='Off'&&$d['dag']->s<-2) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
		finkom();
	}
} else setCache('timestampweg',$time-240);

