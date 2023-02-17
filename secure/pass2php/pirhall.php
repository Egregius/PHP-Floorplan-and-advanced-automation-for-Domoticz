<?php
if ($status=='On'&&$d['auto']['s']=='On') {
    fhall();
    sirene('Beweging hall');
    if ($d['kamer']['m']!=0&&$d['kamer']['s']==0&&past('kamer')<90) {
		storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
	}
}