<?php
if ($status=='On') {
	sw('boseliving', 'On', basename(__FILE__).':'.__LINE__, true);
	storemode('bose101', 1, basename(__FILE__).':'.__LINE__, true);
	sw('bosekeuken', 'On',basename(__FILE__).':'.__LINE__, true);
}
