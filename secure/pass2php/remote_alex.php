<?php
if ($status=='On') {
	sl('alex', 1, basename(__FILE__).':'.__LINE__);
} else {
	sw('alex', 'Off', basename(__FILE__).':'.__LINE__);
}
