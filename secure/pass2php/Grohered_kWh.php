<?php
//lg('grohered='.$status);
if ($status>100&&($d['Xlight']['m']=='groen'||$d['Xlight']['m']=='uit')) {
	rgb('Xlight', 0, 70);
	storemode('Xlight', 'rood');
} elseif ($d['GroheRed']['s']=='On'&&($d['Xlight']['m']=='rood'||$d['Xlight']['m']=='uit')) {
	rgb('Xlight', 115, 30);
	storemode('Xlight', 'groen');
}
