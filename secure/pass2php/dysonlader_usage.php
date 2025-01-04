<?php
if ($status!=$d['dysonlader_usage']['s']) {
	if ($status<15&&$d['dysonlader']['s']=='On'&&past('dysonlader')>1800) sw('dysonlader', 'Off', basename(__FILE__).':'.__LINE__);
}