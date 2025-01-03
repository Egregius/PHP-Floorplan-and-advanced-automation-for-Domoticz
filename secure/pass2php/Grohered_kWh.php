<?php
if ($status>100&&($d['Xlight']['m']=='groen'||$d['Xlight']['m']=='uit')) {
	rgb('Xlight', 0, 70);
	storemode('Xlight', 'rood');
} elseif ($status<100&&$d['GroheRed']['s']=='On'&&($d['Xlight']['m']=='rood'||$d['Xlight']['m']=='uit')) {
	rgb('Xlight', 115, 30);
	storemode('Xlight', 'groen');
	if (past('GroheRed')>300) sw('GroheRed', 'Off');
}
