<?php
if ($status==100&&$d['weg']['s']==0&&$d['dag']['m']>280&&$d['heating']['s']<0&&$d['rwaskamer']['s']>0) sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);