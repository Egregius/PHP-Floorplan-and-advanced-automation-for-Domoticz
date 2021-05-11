<?php
if ($d['GroheRed']['s']=='Off') sw('GroheRed', 'On', basename(__FILE__).':'.__LINE__);

rgb('Xlight', 255, 100);
sl('Xlight', 100, basename(__FILE__).':'.__LINE__);
sleep(1);
rgb('Xlight', 0, 0);
sl('Xlight', 0, basename(__FILE__).':'.__LINE__);

if ($status=='On') sw('werkbladR', 'Off', basename(__FILE__).':'.__LINE__);
