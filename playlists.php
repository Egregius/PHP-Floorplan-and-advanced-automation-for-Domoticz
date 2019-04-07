<?php
/**
 * Pass2PHP
 * php version 7.2.15
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
?>
<html>
    <head>
        <title>Playlists</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
        <meta name="HandheldFriendly" content="true"/>
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/>
        <link rel="icon" type="image/png" href="images/spotify.png"/>
        <link rel="shortcut icon" href="images/spotify.png"/>
        <link rel="apple-touch-startup-image" href="images/spotify.png"/>
        <link rel="apple-touch-icon" href="images/spotify.png"/>
        <link rel="shortcut icon" type="image/png" href="images/spotify.png"/>
        <link rel="shortcut icon" type="image/png" href="images/spotify.png"/>
        <style>
            html{padding:0px;margin:0px;color:#ccc;font-family:sans-serif;}
            body{margin:0px;background:#000;width:100%}
            a:link{text-decoration:none;color:#ccc}
            a:visited{text-decoration:none;color:#ccc}
            a:hover{text-decoration:none;color:#ccc;}
            .btn{height:50px;width:100%;font-size:2em;background-color:#333;color:#ccc;text-align:center;vertical-align:bottom;display:inline-block;border:0px solid transparent;padding:0;margin-bottom:5px;-webkit-appearance:none;}
        </style>
    </head>
    <body>
<?php
$items=array(
    'spotify:user:egregiusspotify:playlist:1hEekeZGrVps1YELGaKVNJ'=>'A Mix',
    'spotify:user:egregiusspotify:playlist:6pCNDDH7cRsXe3qI9c2UgA'=>'Techno, Trance and Retro',
    'spotify:user:egregiusspotify:playlist:3ul0mPAwuFJYjuSN0354hE'=>'Paul Kalkbrenner',
    'spotify:user:egregiusspotify:playlist:5760wAXHz1A2zbYDlksl4X'=>'Tïesto',
    'spotify:user:egregiusspotify:playlist:3KtRpRbISucl80Z9KKIDsF'=>'Happy Music',
    'spotify:user:egregiusspotify:playlist:2MBi7BLjI3IhuW62uqtW95'=>'Love Ballads',
    );
$c=count($items);
if ($c<8) {
    echo '<style>.btn{height:'.((100/$c)-2).'%;}</style>';
}
foreach ($items as $link=>$name) {
    echo '<a href="'.$link.'" class="btn">'.$name.'</a>';
}
?>
    </body>
</html>