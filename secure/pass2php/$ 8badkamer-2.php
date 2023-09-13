<?php
if ($status=='On') {
	sl('lichtbadkamer', 35, basename(__FILE__).':'.__LINE__);
	store('deurbadkamer', $d['deurbadkamer']['s'], basename(__FILE__).':'.__LINE__);
	if ($d['Weg']['s']==1&&$time>strtotime('6:00')&&$time<strtotime('9:00')) huisthuis();
}
