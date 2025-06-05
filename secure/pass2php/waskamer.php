<?php
if ($status>0) {
	if ($d['time']>strtotime('7:00')&&$d['time']<strtotime('10:00')&&$d['ralex']['s']==0) {
		if ($d['rwaskamer']['s']>0) {
			sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}
