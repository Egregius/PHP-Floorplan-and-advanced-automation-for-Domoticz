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
if ($status=='Off') alert('regenput', 'Regenput leeg, zet alle water op stadswater.', 3600, false, true);
else alert('regenput', 'Regenput niet meer leeg, schakel stadswater uit.', 3600, false, true);