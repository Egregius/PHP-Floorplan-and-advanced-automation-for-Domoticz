<?php
/**
 * Pass2PHP
 * php version 8
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['luifel']['s']<30) $level=30;
elseif ($d['luifel']['s']>=30&&$d['luifel']['s']<=90) $level=$d['luifel']['s']+10;
else $level=100;
sl('luifel', $level, basename(__FILE__).':'.__LINE__);
storemode('luifel', 1, basename(__FILE__).':'.__LINE__);
