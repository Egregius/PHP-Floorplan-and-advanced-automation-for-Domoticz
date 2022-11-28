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
if ($d['lichtbadkamer']['s']>0) sl('lichtbadkamer', 0, basename(__FILE__).':'.__LINE__);
if ($d['auto']['s']=='On'&&$d['Weg']['s']==1&&TIME>strtotime('6:00')&&TIME<strtotime('9:00')) store('Weg', 0, basename(__FILE__).':'.__LINE__);
if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off');
if ($d['badkamervuur1']['s']=='On') sw('badkamervuur1', 'Off');
if ($d['badkamer_set']['s']!=16.2) store('badkamer_set', 16.2, basename(__FILE__).':'.__LINE__);
if ($d['badkamer_set']['m']!=0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
douche();
