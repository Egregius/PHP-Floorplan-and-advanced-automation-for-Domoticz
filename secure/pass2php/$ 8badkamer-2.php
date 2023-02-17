<?php
if ($status=='On') {
	sl('lichtbadkamer', 35, basename(__FILE__).':'.__LINE__);
	store('deurbadkamer', $d['deurbadkamer']['s'], basename(__FILE__).':'.__LINE__);
}
