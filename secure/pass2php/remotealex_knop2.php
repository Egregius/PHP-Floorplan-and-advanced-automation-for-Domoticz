<?php
if ($time>=$t&&$status=='Remote_button_short_press') {
	sl('ralex', 0, basename(__FILE__).':'.__LINE__, true);
} elseif ($time>=$t&&$status=='Remote_button_double_press') {
	sl('ralex', 0, basename(__FILE__).':'.__LINE__, true);
}
