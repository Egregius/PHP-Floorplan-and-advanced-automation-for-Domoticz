<?php
if ($d['wasbak']['s']>0) sl('wasbak', 0, basename(__FILE__).':'.__LINE__, true);
if ($d['snijplank']['s']>0) sl('snijplank', 0, basename(__FILE__).':'.__LINE__, true);
if ($d['kookplaat']['s']=='On') sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__, true);
if ($d['GroheRed']['s']=='On') sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__, true);
if ($d['bose105']['s']=='On') {
	bosekey("POWER", 0, 105);
	sw('bose105', 'Off',basename(__FILE__).':'.__LINE__);
}