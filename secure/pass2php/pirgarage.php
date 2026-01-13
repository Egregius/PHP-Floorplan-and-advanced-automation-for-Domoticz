<?php
if ($status=='On'&&$d['auto']['s']=='On'&&$d['weg']['s']==0) fgarage();
elseif ($status=='On'&&$d['weg']['s']>0&&$d['poort']['s']=='Off') sirene('Beweging garage');
