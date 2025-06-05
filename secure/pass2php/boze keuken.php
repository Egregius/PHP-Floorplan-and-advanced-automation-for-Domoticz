<?php
sw('Boze keuken', $status);
if ($status=='Off') {
	if ($d['bose105']['icon']!='Offline') storeicon('bose105', 'Offline', basename(__FILE__).':'.__LINE__);
	if ($d['bose105']['s']=='On') sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
}