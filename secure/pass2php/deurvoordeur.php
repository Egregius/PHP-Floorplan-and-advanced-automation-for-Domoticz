<?php
if ($d['auto']->s=='On') {
	if ($status=="Open"&&$d['voordeur']->s=='Off'&&$d['dag']->s<-2) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	finkom();
}