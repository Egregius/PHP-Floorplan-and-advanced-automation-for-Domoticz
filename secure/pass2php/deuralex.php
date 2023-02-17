<?php
if ($d['auto']['s']=='On') {
	if ($status=='Open') {
		fhall();
	}
}
if ($status=='Open') sirene('Deur Alex open');
else sirene('Deur Alex dicht');
