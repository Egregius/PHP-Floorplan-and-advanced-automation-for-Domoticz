<?php
if ($status=='On') {
	sw('water', 'On', basename(__FILE__).':'.__LINE__);
	storemode('water', 300, basename(__FILE__).':'.__LINE__);
}
