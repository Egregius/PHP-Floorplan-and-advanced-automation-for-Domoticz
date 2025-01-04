<?php
if ($d['Weg']['s']>1) {
	if ($status=='Open') sirene('Raam hall open');
	else sirene('Raam hall dicht');
}