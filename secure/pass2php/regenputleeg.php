<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='Off'&&$d['regenputleeg']['s']=='On') alert('regenput', 'Regenput leeg, zet alle water op stadswater.', 3600, false, true);
elseif ($status=='On'&&$d['regenputleeg']['s']=='Off')  alert('regenput', 'Regenput niet meer leeg, schakel stadswater uit.', 3600, false, true);