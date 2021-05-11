<?php
if ($d['GroheRed']['s']=='Off') sw('GroheRed', 'On', basename(__FILE__).':'.__LINE__);

$level=$d['Xlight']['s'];
rgb('Xlight', $rgb, 100);
sl('Xlight', 100, basename(__FILE__).':'.__LINE__);
sleep(2);
rgb('Xlight', $rgb, $level);
sl('Xlight', $level, basename(__FILE__).':'.__LINE__);

if ($status=='On') sw('werkbladR', 'Off', basename(__FILE__).':'.__LINE__);
