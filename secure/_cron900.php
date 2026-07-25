<?php
setNextubeMode();

if (past('weg')>18000&& $d['weg']->s==0&& past('pirliving')>18000&& past('pirkeuken')>18000&& past('pirinkom')>18000&& past('pirhall')>18000&& past('pirgarage')>18000) {
	store('weg', 1, basename(__FILE__).':'.__LINE__);
	alert('WEG','Slapen ingeschakeld na 5 uur geen beweging',60,false,3);
} elseif (past('weg')>36000&& $d['weg']->s==1&& past('pirliving')>36000&& past('pirkeuken')>36000&& past('pirinkom')>36000&& past('pirhall')>36000&& past('pirgarage')>36000) {
	store('weg', 2, basename(__FILE__).':'.__LINE__);
	alert('WEG','Weg ingeschakeld na 10 uur geen beweging',60,false,3);
} elseif (past('weg')>86400&& $d['weg']->s==2&& past('pirliving')>86400&& past('pirkeuken')>86400&& past('pirinkom')>86400&& past('pirhall')>86400&& past('pirgarage')>86400) {
	store('weg', 3, basename(__FILE__).':'.__LINE__);
	alert('WEG','Vakantie ingeschakeld na 24 uur geen beweging',60,false,3);
}