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
if ($status=="Open"&&$d['auto']['s']=='On') {
    finkom();
    fliving();
}
if ($status=='Open') sirene('Deur inkom open');
else sirene('Deur inkom dicht');