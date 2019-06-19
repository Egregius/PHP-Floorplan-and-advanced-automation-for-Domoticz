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
if ($status=='On'&&past('8badkamer-7')>5) {
    if ($d['badkamervuur1']['s']=='On') {
        store('badkamer_set', 10);
        $d['badkamer_set']['s']=10;
    } else {
        store('badkamer_set', 22.5);
        $d['badkamer_set']['s']=22.5;
        $d['deurbadkamer']['s']='Closed';
    }
    store('deurbadkamer', $d['deurbadkamer']['s']);
    douche();
    $d['lichtbadkamer']['s']=25;
    $d['deurbadkamer']['s']='Closed';
    $d['8badkamer-7']['t']=0;
    $d['badkamervuur1']['t']=0;
    $d['badkamervuur2']['t']=0;
    require '_verwarming.php';
    resetsecurity();
}