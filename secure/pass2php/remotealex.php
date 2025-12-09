<?php
if ($status=='1_single') {
//	lg(basename(__FILE__).':'.__LINE__.' '.$d['alex']['s']);
	if ($d['alex']['s']==0) sl('alex', 1, basename(__FILE__).':'.__LINE__, true);
	elseif ($time>=$t&&$d['alex']['s']==1) sl('alex', 45, basename(__FILE__).':'.__LINE__, true);
	elseif($time>=$t) sl('alex', 100, basename(__FILE__).':'.__LINE__, true);
} elseif ($status=='1_double') {
	if($time>=$t) sl('alex', 100, basename(__FILE__).':'.__LINE__, true);
} elseif ($time>=$t&&$status=='2_single') {
	sl('ralex', 0, basename(__FILE__).':'.__LINE__, true);
} elseif ($time>=$t&&$status=='2_double') {
	sl('ralex', 0, basename(__FILE__).':'.__LINE__, true);
} elseif ($status=='3_single') {
//	lg(basename(__FILE__).':'.__LINE__.' '.$d['alex']['s']);
	if ($d['alex']['s']>45) sl('alex', 45, basename(__FILE__).':'.__LINE__, true);
	elseif ($d['alex']['s']>1) sl('alex', 1, basename(__FILE__).':'.__LINE__, true);
	else sl('alex', 0, basename(__FILE__).':'.__LINE__, true);
} elseif ($status=='3_double') {
//	lg(basename(__FILE__).':'.__LINE__.' '.$d['alex']['s']);
	sl('alex', 0, basename(__FILE__).':'.__LINE__, true);
} elseif ($status=='4_single'&&$time>strtotime('11:00')) {
	sl('ralex', 100, basename(__FILE__).':'.__LINE__, true);
} elseif ($status=='4_double'&&$time>strtotime('11:00')) {
	sl('ralex', 100, basename(__FILE__).':'.__LINE__, true);
}

