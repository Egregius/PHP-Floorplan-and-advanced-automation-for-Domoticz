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
if ($d['deurbadkamer']['s']=='Open'&&$status=='Off'&&$d['badkamervuur1']['s']=='On') {
    sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
    sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
}
