<?php
$ctx=stream_context_create(array('http'=>array('timeout'=>1)));
if ($status=='On') {
	store('weg', 0, basename(__FILE__).':'.__LINE__);

}