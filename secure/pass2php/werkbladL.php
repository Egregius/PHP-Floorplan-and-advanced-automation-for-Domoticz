<?php
if ($d['wasbak']['s']>0) sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
if ($d['GroheRed']['s']=='On') sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['kookplaatpower']['s']=='On') sw('kookplaatpower', 'Off', basename(__FILE__).':'.__LINE__);
if ($status=='On') sw('werkbladL', 'Off', basename(__FILE__).':'.__LINE__);
