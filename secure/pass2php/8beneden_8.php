<?php
if ($status=='On'&&(past('pirliving')<300||past('lgtv')<300)) {
	sw('zetel', 'On', basename(__FILE__).':'.__LINE__, true);
}