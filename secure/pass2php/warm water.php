<?php
if ($status=='On') {
	sw('GroheRed', 'On');
	storemode('GroheRed', 1);
} else {
	sw('GroheRed', 'Off');
	storemode('GroheRed', 0);
}
