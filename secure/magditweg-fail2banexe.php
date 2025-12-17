#!/usr/bin/php -n
<?php
file_get_contents('https://mynetpay.be/fail2ban.php?token=abc&action='.$_SERVER["argv"][1].'&ip='.$_SERVER["argv"][2]);