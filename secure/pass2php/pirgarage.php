<?php
if ($status=='On'&&$d['auto']['s']=='On') {
    fgarage();
}
if ($d['Weg']['s']>0&&$d['poortrf']['s']=='Off') sirene('Beweging garage');
