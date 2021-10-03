<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['badkamervuur1']['s']=='On') sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
douche();
