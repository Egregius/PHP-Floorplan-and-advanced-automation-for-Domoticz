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
if ($d['badkamer_set']['s']>18) {
    store('badkamer_set', 10);
    $d['badkamer_set']['s']=10;
} else {
    store('deurbadkamer', 'Closed');
    store('badkamer_set', 22.5);
    $d['badkamer_set']['s']=22.5;
    $d['deurbadkamer']['s']='Closed';
}
douche();
$d['lichtbadkamer']['s']=25;
$d['deurbadkamer']['s']='Closed';
$d['8badkamer7']['t']=0;
$d['badkamervuur1']['t']=0;
$d['badkamervuur2']['t']=0;
require '_verwarming.php';
resetsecurity();