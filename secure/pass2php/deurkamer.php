<?php
if ($d['auto']['s']=='On') {
	if ($status=='Open') {
		fhall();
	}
}
if ($status=='Open') sirene('Deur kamer open');
else sirene('Deur kamer dicht');
