<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='Off'&&$d['regenputvol']['s']=='On') alert('regenput', 'Regenput niet meer vol.', 3600, false, true);
elseif ($status=='On'&&$d['regenputvol']['s']=='Off') alert('regenput', 'Regenput vol.', 3600, false, true);
