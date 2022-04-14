<?php
if ($status=='On') {
	if ($d['Rliving']['s']<100) sl('Rliving', 100, basename(__FILE__).':'.__LINE__);
	if ($d['Rbureel']['s']<100) sl('Rbureel', 100, basename(__FILE__).':'.__LINE__);
	if ($d['RkeukenL']['s']<100) sl('RkeukenL', 100, basename(__FILE__).':'.__LINE__);
	if ($d['RkeukenR']['s']<100) sl('RkeukenR', 100, basename(__FILE__).':'.__LINE__);
} elseif ($status=='Off') {
	if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
	if ($d['Rbureel']['s']>0) sl('Rbureel', 0, basename(__FILE__).':'.__LINE__);
	if ($d['RkeukenL']['s']>0) sl('RkeukenL', 0, basename(__FILE__).':'.__LINE__);
	if ($d['RkeukenR']['s']>0) sl('RkeukenR', 0, basename(__FILE__).':'.__LINE__);
}
