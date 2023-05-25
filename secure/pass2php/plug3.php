<?php
store('Boze keuken', $status);
file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx=3635&switchcmd='.$status);