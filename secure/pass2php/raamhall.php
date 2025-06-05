<?php
if ($d['weg']['s']>1) {
	if ($status=='Open') sirene('Raam hall open');
	else sirene('Raam hall dicht');
}