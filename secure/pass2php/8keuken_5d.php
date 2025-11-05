<?php
if ($status=='On') {
	if ($d['wasbak']['s']>0) sl('wasbak', 0, basename(__FILE__).':'.__LINE__, true);
	if ($d['snijplank']['s']>0) sl('snijplank', 0, basename(__FILE__).':'.__LINE__, true);
	if ($d['kookplaat']['s']=='On') sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__, true);
	if ($d['grohered']['s']=='On'&&$d['z']==0) sw('grohered', 'Off', basename(__FILE__).':'.__LINE__, true);
	if ($d['bosekeuken']['s']=='On') sw('bosekeuken', 'Off',basename(__FILE__).':'.__LINE__);
}