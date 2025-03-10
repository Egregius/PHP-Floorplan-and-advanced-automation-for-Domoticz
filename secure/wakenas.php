<?php
require 'functions.php';
lg('Waking NAS for backup');
shell_exec('wakenas.sh');