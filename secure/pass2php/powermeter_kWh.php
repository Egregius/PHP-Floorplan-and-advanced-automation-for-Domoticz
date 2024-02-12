<?php
if ($status<2200&&$d['powermeter']['s']=='On'&&past('powermeter')>3600) sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
//telegram($status);