<?php
$n='alex';
if ($status>$d[$n.'_temp']['s']+0.1) $status=$d[$n.'_temp']['s']+0.1;
elseif ($status<$d[$n.'_temp']['s']-0.1) $status=$d[$n.'_temp']['s']-0.1;
$d[$n.'_temp']['s']=$status;