<?php
if ($d['kamer']->s>0) sl('kamer',0);
else {
	if ($d['time']>=strtotime('9:00')&&$d['time']<strtotime('21:00')) hass('light', 'turn_on', 'light.kamer', ['brightness_pct' => 100,'color_temp_kelvin' => 4000]);
	else hass('light', 'turn_on', 'light.kamer', ['brightness_pct' => 10,'color_temp_kelvin' => 2202]);
}