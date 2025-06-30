<?php
lg($device.'='.$status.'>'.$d['bureel']['s']);
if ($d['bureel']['s']=='Off'||$d['bureel']['s']==0) sl('bureel', 50);
else sl('bureel', 0);