<?php
if ($status!=$d['kWh_Meter']['s']) $db->query("INSERT INTO kWh_Meter (stamp, value) VALUES ('".strftime("%F %T", $_SERVER['REQUEST_TIME'])."', '".$status."');");
