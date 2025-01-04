<?php
if ($status=="On"&&$d['auto']['s']=='On'&&$d['Weg']['s']==0) finkom();
elseif ($status=='On'&&$d['Weg']['s']>0&&past('Weg')>60) sirene('Beweging inkom');