<?php
if ($d['wasbak']['s']>0) sl('wasbak', 0, basename(__FILE__).':'.__LINE__, true);
if ($d['snijplank']['s']>0) sl('snijplank', 0, basename(__FILE__).':'.__LINE__, true);
if ($d['kookplaat']['s']=='On') sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__, true);
if ($d['GroheRed']['s']=='On'&&$d['zon']==0) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__, true);
if ($d['bose102']['s']=='On') {
	bosekey("POWER", 0, 102);
	sw('bose102', 'Off',basename(__FILE__).':'.__LINE__);
}