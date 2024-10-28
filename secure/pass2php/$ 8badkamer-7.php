<?php
if ($d['heating']['s']>=0) {
	store('badkamer_set', 21, basename(__FILE__).':'.__LINE__);
	storemode('badkamer_set', 2, basename(__FILE__).':'.__LINE__);
	if ($d['waskamervuur1']['s']=='Off') sw('waskamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	if ($d['waskamervuur2']['s']=='Off') sw('waskamervuur2', 'On', basename(__FILE__).':'.__LINE__);
}
