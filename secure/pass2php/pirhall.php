<?php
if ($status=='On'&&$d['auto']['s']=='On') {
    fhall();
}
if ($status=='On'&&$d['Weg']['s']>1) sirene('Beweging hall');