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
double('dampkap', 'On');
if (TIME>$d['dampkap']['m']) storemode('dampkap', TIME+900);
else storemode('dampkap', $d['dampkap']['m']+900);
resetsecurity();
