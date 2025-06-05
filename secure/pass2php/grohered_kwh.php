<?php
if ($status<100&&$d['grohered']['s']=='On'&&$d['net']>0) {
	if (past('grohered')>300) sw('grohered', 'Off', basename(__FILE__).':'.__LINE__);
}