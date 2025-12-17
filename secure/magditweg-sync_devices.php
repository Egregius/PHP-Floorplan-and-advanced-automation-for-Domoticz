<?php
require 'functions.php';
$d=fetchdata(0,'sync_devices');
sync_devices_if_changed($db, $d);