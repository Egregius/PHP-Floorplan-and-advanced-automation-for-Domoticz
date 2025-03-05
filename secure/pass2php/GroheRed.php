<?php
if ($status=='Off') {
	file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx=744&switchcmd=Off&level=0&passcode=');
}

