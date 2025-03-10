<?php
if ($status<100&&$d['GroheRed']['s']=='On'&&$d['net']>0) {
	if (past('GroheRed')>300) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
}