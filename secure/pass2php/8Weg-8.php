<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='On') {
    store('Weg', 0);
    sw('poortrf', 'On');
    lgsql('Remote','Weg','Thuis');
    if ($d['sirene']['s']!='Group Off') {
        double('sirene', 'Off');
    }
}