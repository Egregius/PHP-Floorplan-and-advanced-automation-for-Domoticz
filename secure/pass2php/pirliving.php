<?php
if ($status=='On'&&$d['auto']['s']=='On'&&$d['Weg']['s']==0) {
	fliving();
}
if ($status=='On'&&$d['Weg']['s']>0) sirene('Beweging living');