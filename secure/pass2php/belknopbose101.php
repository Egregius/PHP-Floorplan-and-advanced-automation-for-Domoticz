<?php
include '../functions.php';
//boseplayinfo("doorbell", 60, basename(__FILE__).':'.__LINE__, 101);
lg('belknopbose101.php','sensor');
echo Wiim("setPlayerCmd:play:" . urlencode("http://192.168.2.2/sounds/doorbell.mp3"));
sleep(8);
$preset=wiimplaylist();
echo Wiim("MCUKeyShortClick:$preset");