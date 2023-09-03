<?php
if ($status=='On') {
	sl('waskamer', 0, basename(__FILE__).':'.__LINE__, true);
	if ($d['Weg']['s']==1) finkom(true);
	huisthuis();
}
