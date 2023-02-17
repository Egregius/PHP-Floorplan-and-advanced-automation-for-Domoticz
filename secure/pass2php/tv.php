<?php
if ($status=='Off') {
	if ($d['lgtv']['s']!='Off') store('lgtv', 'Off', basename(__FILE__).':'.__LINE__);
}
