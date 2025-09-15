<?php
if ($status=='Remote_button_short_press') {
	lg($d['alex']['s']);
	if ($d['alex']['s']==0) sl('alex', 1, basename(__FILE__).':'.__LINE__, true);
	elseif ($time>=$t&&$d['alex']['s']==1) sl('alex', 45, basename(__FILE__).':'.__LINE__, true);
	elseif($time>=$t) sl('alex', 100, basename(__FILE__).':'.__LINE__, true);
} elseif ($status=='Remote_button_double_press') {
	if($time>=$t) sl('alex', 100, basename(__FILE__).':'.__LINE__, true);
}
