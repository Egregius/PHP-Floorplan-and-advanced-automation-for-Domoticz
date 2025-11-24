<?php
if ($status>0) {
	if ($d['badkamerpower']['s']!='On') sw('badkamerpower', 'On', basename(__FILE__).':'.__LINE__);
} else {
	if ($d['badkamerpower']['s']!='Off') sw('badkamerpower', 'Off', basename(__FILE__).':'.__LINE__);
}
