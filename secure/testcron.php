<?php
include 'functions.php';
$d=fetchdata();
include '_cron'.$_REQUEST['cron'].'.php';