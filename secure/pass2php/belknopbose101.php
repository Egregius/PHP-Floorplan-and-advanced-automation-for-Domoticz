<?php
include '../functions.php';
//boseplayinfo("doorbell", 60, basename(__FILE__).':'.__LINE__, 101);
Wiim("setPlayerCmd:playPromptUrl:" . urlencode("http://192.168.2.2/sounds/doorbell.mp3"));
sleep(5);
$preset=wiimplaylist();
Wiim("MCUKeyShortClick:$preset");