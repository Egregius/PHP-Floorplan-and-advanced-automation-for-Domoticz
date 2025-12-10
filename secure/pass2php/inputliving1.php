<?php
if ($d['bureelrechts']['s']==0) {
	sl('bureelrechts', 50, basename(__FILE__).':'.__LINE__);
} else sl('bureelrechts', 0, basename(__FILE__).':'.__LINE__);