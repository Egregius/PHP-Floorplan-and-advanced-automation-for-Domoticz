<?php
require 'functions.php';
$d=fetchdata();
if ($d['bose101']['s']=='On'||$d['bose105']['s']=='On') echo 1;
else echo 0;