<?php
if ($status=='Open'&&$d['time']>strtotime('6:00')&&$d['time']<strtotime('12:00')&&$d['ralex']->s<=1&&$d['rwaskamer']->s>0) sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);
