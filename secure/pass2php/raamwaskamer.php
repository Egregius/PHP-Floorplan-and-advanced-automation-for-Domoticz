<?php
if ($status=='Open'&&TIME>strtotime('6:00')&&TIME<strtotime('12:00')&&$d['Ralex']['s']<=1&&$d['Rwaskamer']['s']>1) sl('Rwaskamer', 1, basename(__FILE__).':'.__LINE__);
