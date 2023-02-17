<?php
if ($d['Rliving']['s']<30) {
	sl('Rliving', 30, basename(__FILE__).':'.__LINE__);
}
if ($d['Rbureel']['s']<40) {
	sl('Rbureel', 40, basename(__FILE__).':'.__LINE__);
}
store('Weg', 0, basename(__FILE__).':'.__LINE__);
