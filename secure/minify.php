<?php
require "php-html-css-js-minifier.php";
$str = file_get_contents('/var/www/html/scripts/floorplan.js');

// echo
echo fn_minify_js($str);

//­ save as `style.min.css`
file_put_contents('/var/www/html/scripts/floorplan.min.js', fn_minify_js($str));