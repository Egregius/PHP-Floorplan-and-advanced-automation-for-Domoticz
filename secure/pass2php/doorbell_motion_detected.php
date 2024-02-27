<?php
if ($status=='On') {
	if ($d['dag']<2) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
}