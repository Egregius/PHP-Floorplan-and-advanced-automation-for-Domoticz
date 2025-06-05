<?php
if ($status=='On') {
	store('auto', 'Off', basename(__FILE__).':'.__LINE__);
	sw('garage', 'On', basename(__FILE__).':'.__LINE__);
}