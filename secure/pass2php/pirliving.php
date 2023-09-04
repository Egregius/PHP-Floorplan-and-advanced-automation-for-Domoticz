<?php
if ($status=='On'&&$d['auto']['s']=='On') {
	fliving();
}
if ($status=='On'&&$d['Weg']['s']>0) sirene('Beweging living');