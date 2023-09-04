<?php
if ($status=="On"&&$d['auto']['s']=='On') {
    finkom();
}
if ($status=='On'&&$d['Weg']['s']>0) sirene('Beweging inkom');