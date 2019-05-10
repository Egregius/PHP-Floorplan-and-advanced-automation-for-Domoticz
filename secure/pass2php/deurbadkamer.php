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
if ($status=='Open'&&$d['auto']['s']) {
    fbadkamer();
} else {
    if (past('8badkamer-8')>10) {
        $d['lichtbadkamer']['s']=25;
        $d['deurbadkamer']['s']='Closed';
        $d['8badkamer-7']['t']=0;
        $d['badkamervuur1']['t']=0;
        $d['badkamervuur2']['t']=0;
        require '_verwarming.php';
    }
}