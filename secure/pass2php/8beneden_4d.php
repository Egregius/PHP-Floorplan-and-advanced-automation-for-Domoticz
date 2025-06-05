<?php
if ($status=='On') {
	sw('lampkast', 'Off', basename(__FILE__).':'.__LINE__, true);
}