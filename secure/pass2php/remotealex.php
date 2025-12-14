<?php
if ($status=='1_single') {
	if ($d['alex']['s']==0) sl('alex', 1);
	elseif ($time>=$t&&$d['alex']['s']==1) sl('alex', 45);
	elseif($time>=$t) sl('alex', 100);
} elseif ($status=='1_double') {
	if($time>=$t) sl('alex', 100);
} elseif ($time>=$t&&$status=='2_single') {
	sl('ralex', 0);
} elseif ($time>=$t&&$status=='2_double') {
	sl('ralex', 0);
} elseif ($status=='3_single') {
	if ($d['alex']['s']>45) sl('alex', 45);
	elseif ($d['alex']['s']>1) sl('alex', 1);
	else sl('alex', 0);
} elseif ($status=='3_double') {
	sl('alex', 0);
} elseif ($status=='4_single'&&$time>strtotime('11:00')) {
	sl('ralex', 100);
} elseif ($status=='4_double'&&$time>strtotime('11:00')) {
	sl('ralex', 100);
}

