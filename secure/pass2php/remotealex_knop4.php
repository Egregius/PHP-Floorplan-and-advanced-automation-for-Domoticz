<?php
if ($status=='Remote_button_short_press'&&$time>strtotime('11:00')) {
	sl('ralex', 100, basename(__FILE__).':'.__LINE__, true);
} elseif ($status=='Remote_button_double_press'&&$time>strtotime('11:00')) {
	sl('ralex', 100, basename(__FILE__).':'.__LINE__, true);
}
