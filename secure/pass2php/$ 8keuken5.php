<?php
if ($d['wasbak']['s']>0) $level=$d['wasbak']['s']-10;
else $level=0;
if ($level<0) $level=0;
sl('wasbak', $level, basename(__FILE__).':'.__LINE__);
