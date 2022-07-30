<?php
if ($status=='Open'&&$d['daikin']['m']==0&&$d['daikin']['s']=='On') daikinset('kamer', 0, 3, 20, basename(__FILE__).':'.__LINE__, 'A', 40);
