<?php
if ($d['lgtv']['s']!='Off') {
	shell_exec('python3 secure/lgtv.py -c off '.$lgtvip);
}
