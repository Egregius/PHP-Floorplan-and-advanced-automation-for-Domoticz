<?php
if ($status=='On') {
	if($d['water']->s=='On') sw('water', 'Off', basename(__FILE__).':'.__LINE__);
	else sw('water', 'On', basename(__FILE__).':'.__LINE__);
}