<?php
setNextubeMode();

if ($d['steenterras']->s=='Off'&&rollingAbove('b', 0, 12)&&$d['c']>30&&rollingBelow('n', -1200, 6)) {
	sw('steenterras','On',basename(__FILE__).':'.__LINE__);
	$steenautomatischaan=true;
} elseif($d['steenterras']->s=='On'&&$steenautomatischaan==true&&rollingAbove('n', 0, 12)) {
	sw('steenterras','Off',basename(__FILE__).':'.__LINE__);
	$steenautomatischaan=false;
}