<?php
foreach(array('Rliving', 'Rbureel', 'RkeukenL', 'RkeukenR') as $i) {
	if ($d[$i]['s']!=0) sl($i, 0, basename(__FILE__).':'.__LINE__);
}
