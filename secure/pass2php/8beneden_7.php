<?php
if ($status=='On') {
	sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
	storemode('bose101', 1, basename(__FILE__).':'.__LINE__);
}