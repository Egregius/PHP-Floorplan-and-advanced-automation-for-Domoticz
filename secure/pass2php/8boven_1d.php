<?php
if ($status=='On') {
	if ($d['waskamer']->s<30) sl('waskamer', 30, basename(__FILE__).':'.__LINE__, true);
}
