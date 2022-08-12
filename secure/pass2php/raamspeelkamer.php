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
if ($status=='Open'&&TIME>strtotime('6:00')&&TIME<strtotime('12:00')&&$d['Ralex']['s']<=1&&$d['Rspeelkamer']['s']>1) sl('Rspeelkamer', 1, basename(__FILE__).':'.__LINE__);
