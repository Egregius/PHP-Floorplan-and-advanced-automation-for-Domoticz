<?php
if ($d['heating']['s']>=0) {
	store('badkamer_set', 19, basename(__FILE__).':'.__LINE__);
	storemode('badkamer_set', 1, basename(__FILE__).':'.__LINE__);
	if ($d['waskamervuur1']['s']=='Off') sw('waskamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	if ($d['waskamervuur2']['s']=='Off') sw('waskamervuur2', 'On', basename(__FILE__).':'.__LINE__);
}
