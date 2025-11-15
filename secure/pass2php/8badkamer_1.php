<?php
if ($status=='On') {
	sl('lichtbadkamer', 20, basename(__FILE__).':'.__LINE__);
	store('deurbadkamer', $d['deurbadkamer']['s'], basename(__FILE__).':'.__LINE__);
	if ($d['weg']['s']==1&&$d['living_set']['m']==0&&$d['time']>$t-7200&&$d['time']<$t) storemode('living_set', 2, basename(__FILE__) . ':' . __LINE__);
}
