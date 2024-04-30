<?php
if ($status=="On"&&$d['auto']['s']=='On'&&$d['Weg']['s']==0) {
    finkom();
}
if ($status=='On'&&$d['Weg']['s']>0) sirene('Beweging inkom');