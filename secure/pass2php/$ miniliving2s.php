<?php
if (past('$ miniliving2s')>2) {
	shell_exec('/var/www/html/secure/lgtv.py -c pause '.$lgtvip);
}
store('Weg', 0, basename(__FILE__).':'.__LINE__);
