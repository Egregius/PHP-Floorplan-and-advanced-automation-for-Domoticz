<?php
//if($status=='On') hassAddon('d5369777_music_assistant_beta','start');
//else
if($status=='Off') {
	if($d['music_assistant_beta']->s!='Off') sw('music_assistant_beta','Off',basename(__FILE__).':'.__LINE__);
}