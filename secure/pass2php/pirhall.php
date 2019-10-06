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
if ($status=='On'&&$d['auto']['s']=='On') {
    fhall();
    sirene('Beweging hall');
    if ($d['kamer']['m']!=0&&$d['kamer']['s']==0&&past('kamer')<90) {
		storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
	}
}