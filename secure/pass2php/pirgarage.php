<?php
if ($status=='On'&&$d['auto']['s']=='On') {
    fgarage();
}
if ($status=='On'&&$d['Weg']['s']>0&&$d['poortrf']['s']=='Off') sirene('Beweging garage');
