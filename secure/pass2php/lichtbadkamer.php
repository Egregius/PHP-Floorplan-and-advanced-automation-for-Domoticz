<?php
lg(basename(__FILE__).':'.__LINE__.' $status='.$status);
if ($status>0) {
	if ($d['badkamerpower']['s']!='On') sw('badkamerpower', 'On', basename(__FILE__).':'.__LINE__);
} else {
//	if ($d['badkamerpower']['s']!='Off'&&past('badkamerpower')>20) sw('badkamerpower', 'Off', basename(__FILE__).':'.__LINE__);
}
