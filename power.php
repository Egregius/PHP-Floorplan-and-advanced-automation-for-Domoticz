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
$start=microtime(true);
require 'secure/settings.php';
if ($home) {
    error_reporting(E_ALL);ini_set("display_errors", "on");
    ?>
<html><head>
        <title>Power usage</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
        <meta name="HandheldFriendly" content="true"/>
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/>
        <meta name="msapplication-TileColor" content="#000000">
        <meta name="msapplication-TileImage" content="images/domoticzphp48.png">
        <meta name="msapplication-config" content="/browserconfig.xml">
        <link rel="manifest" href="/manifests/floorplan.json">
        <meta name="theme-color" content="#000000">
        <link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
        <link rel="shortcut icon" href="images/domoticzphp48.png"/>
        <link rel="apple-touch-startup-image" href="images/domoticzphp450.png"/>
        <link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
        <link rel="stylesheet" type="text/css" href="/styles/floorplan.php">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
        <style>
            table{border-collapse:collapse;}
            table,th,td{text-align:center;}
            .dataTables_filter{display:none;}
            .odd{}
            .even{}
            table.dataTable tbody tr {
                background-color: #000;
            }
            table.dataTable tbody td {
                padding: 2px 0px;
            }
            .dataTables_info{display:none;}
        </style>
        <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
        $('#example').DataTable( {
            "bPaginate": false,
            "order": [[ 2, "desc" ]]
        } );
    } );
    </script>
    </head>
    <body><br/><br/><br/><br/><br/><br/>
    <table id="example" class="pretty" cellpadding="4" cellspacing="0" width="90%" align="center"><thead>
    <tr><td><br/>Naam</td><td>Usage<br/>Watt</td><td>Today<br/>kWh</td><td>Total<br/>kWh</td></tr></thead><tbody>
    <?php
    $sumusage=0;
    $sumtoday=0;
    $sumtotal=0;
    $domoticz=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=devices&plan=3'), true);
    if ($domoticz) {
        foreach ($domoticz['result'] as $dom) {
            $usage=str_replace(' Watt', '', $dom['Usage']);
            $sumusage=$sumusage+$usage;
            $today=str_replace(' kWh', '', $dom['CounterToday']);
            $sumtoday=$sumtoday+$today;
            $total=str_replace(' kWh', '', $dom['Data']);
            $sumtotal=$sumtotal+$total;
            echo '<tr><td>'.str_replace('kWh_', '', $dom['Name']).'</td><td>'.number_format($usage, 0).'</td><td>'.number_format($today, 2).'</td><td>'.number_format($total, 1).'</td></tr>';
        }
        unset($domoticz, $dom);

    }
    echo '<tr><td><br/></td><td></td><td></td><td></td></tr>
<tr><td><b><big>Total</big></b></td><td><b><big>'.number_format($sumusage, 0).'</big></b></td><td><b><big>'.number_format($sumtoday, 2).'</big></b></td><td><b><big>'.number_format($sumtotal, 1).'</big></b></td></tr>
</tbody></table>
<div class="fix z1" style="top:5px;left:5px;"><a href=\'javascript:navigator_Go("floorplan.php");\'><img src="/images/close.png" width="72px" height="72px"/></a></div>';

} else {
    header("Location: index.php");
    die("Redirecting to: index.php");
}
?>
<script type="text/javascript">setTimeout('window.location.href=window.location.href;',59963);function navigator_Go(url){window.location.assign(url);}</script>
</body></html>