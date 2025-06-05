<?php
if ($status=='On'&&$d['auto']['s']=='On'&&$d['weg']['s']==0) fliving();
elseif ($status=='On'&&$d['weg']['s']>0&&past('weg')>60) sirene('Beweging living');