<?php
/**
 * Pass2PHP
 * php version 7.2.15
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['deurbadkamer']['s']=='Open'&&$status=='Off'&&$d['badkamervuur1']['s']=='On') {
    sw('badkamervuur2', 'Off');
    sw('badkamervuur1', 'Off');
}
