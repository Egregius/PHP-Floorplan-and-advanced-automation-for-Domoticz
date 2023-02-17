<?php
if (past('$ miniliving1l')>2) {
	sw('lamp kast', 'Toggle', basename(__FILE__).':'.__LINE__);
	store('Weg', 0, basename(__FILE__).':'.__LINE__);
}
