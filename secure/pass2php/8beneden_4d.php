<?php
if ($status=='On') {
	sw('lampkast', 'Off', basename(__FILE__).':'.__LINE__);
}
if (!isset($weegschaalfetch)||$weegschaalfetch<$time-300) {
	lg('Fetch weegschaal');
	exec('curl -s http://192.168.2.20/secure/runsync.php?sync=weegschaal &');
	$weegschaalfetch=$time;
}
