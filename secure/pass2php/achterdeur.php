<?php
if ($status=="Open") {
	if ($d['weg']->s>0) sirene('Achterdeur open');
 } else {
	if ($d['weg']->s>0&&$d['auto']->s==1&&past('weg')>178) {
			sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
			telegram('Achterdeur dicht om '.date('G:i:s'), false, 3);
	}

}

// Indien geen zwembad

//if ($status=="Open") {
//	if ($d['steenterras']->s=='Off') sw('steenterras','On', basename(__FILE__).':'.__LINE__);
//	if ($d['houtterras']->s=='Off') sw('houtterras','On', basename(__FILE__).':'.__LINE__);
//}
