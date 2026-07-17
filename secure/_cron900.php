<?php
setNextubeMode();

if ($d['steenterras']->s=='Off'&&$d['b']>600&&$d['c']>30&&$d['n']<-100) {
	sw('steenterras','On',basename(__FILE__).':'.__LINE__);
	$steenautomatischaan=true;
} elseif($d['steenterras']->s=='On'&&$steenautomatischaan==true&&$d['n']>100) {
	sw('steenterras','Off',basename(__FILE__).':'.__LINE__);
	$steenautomatischaan=false;
}