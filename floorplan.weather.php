<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * This is the weather floorplan.
 * It shows the information stored from darksky and openweathermap.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
require 'secure/authentication.php';
require 'scripts/chart.php';
if ($home) {
	$d=fetchdata();
    createheader();
    $ds=json_decode(file_get_contents('/temp/ds.json'));
    unset($ds->minutely);
	$ow=json_decode(file_get_contents('/temp/ow.json'));
    //echo '<pre>';print_r($ds);echo '</pre>';
    //echo '<pre>';print_r($ow);echo '</pre>';
	foreach ($ds->hourly->data as $i) {
	    $hregen[$i->time]['time']=strftime("%H:%m", $i->time);
	    $htemp[$i->time]['time']=strftime("%H:%m", $i->time);
	    $hwind[$i->time]['time']=strftime("%H:%m", $i->time);
	    $hregen[$i->time]['regen']=$i->precipProbability;
	    $htemp[$i->time]['temp']=$i->temperature;
	    $hwind[$i->time]['wind']=$i->windSpeed;
	    $hwind[$i->time]['gust']=$i->windGust;
	}
	foreach ($ds->daily->data as $i) {
	    $dregen[$i->time]['time']=strftime("%a", $i->time);
	    $dtemp[$i->time]['time']=strftime("%a", $i->time);
	    $dwind[$i->time]['time']=strftime("%a", $i->time);
	    $dregen[$i->time]['regen']=$i->precipProbability;
	    $dtemp[$i->time]['low']=$i->temperatureLow;
	    $dtemp[$i->time]['high']=$i->temperatureHigh;
	    $dwind[$i->time]['wind']=$i->windSpeed;
	    $dwind[$i->time]['gust']=$i->windGust;
	}
    if ($udevice=='iPad') {
        $args=array('width'=>1000,'hide_legend'=>false,'responsive'=>false,'background_color'=>'#000','y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCC'),'x_axis_text_style'=>array('fontSize'=>15,'color'=>'CCC'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'));
    } elseif ($udevice=='iPhone') {
        $args=array('width'=>320,'hide_legend'=>false,'responsive'=>false,'background_color'=>'#000','y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCC'),'x_axis_text_style'=>array('fontSize'=>15,'color'=>'CCC'),'text_style'=>array('fontSize'=>12,'color'=>'CCC'));
    } elseif ($udevice=='Mac') {
        $args=array('width'=>490,'hide_legend'=>false,'responsive'=>false,'background_color'=>'#000','y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCC'),'x_axis_text_style'=>array('fontSize'=>15,'color'=>'CCC'),'text_style'=>array('fontSize'=>12,'color'=>'CCC'));
    } else {
        $args=array('width'=>480,'hide_legend'=>false,'responsive'=>false,'background_color'=>'#000','y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCC'),'x_axis_text_style'=>array('fontSize'=>15,'color'=>'CCC'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'));
    }
    $argstemp=array_merge($args, array('height'=>200,'colors'=>array('#FF0', '#F70'),'margins'=>array(0,0,50,50),'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}'));
    $argsregen=array_merge($args, array('chart'=>'ColumnChart','height'=>100,'colors'=>array('#33F'),'margins'=>array(0,0,0,50),'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"},vAxis:{viewWindowMode:\'explicit\',viewWindow:{min:0,max:1}}'));
    $argswind=array_merge($args, array('height'=>100,'colors'=>array('#A66'),'margins'=>array(0,0,0,50),'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}'));
    echo '
	<body>
	    <div class="fix z" id="clock"><a href=\'javascript:navigator_Go("floorplan.weather.php");\' id="time">Refresh</a></div>
	    <div class="fix" style="top:5px;left:5px;"><a href=\'javascript:navigator_Go("floorplan.php");\'><img src="/images/close.png" width="72px" height="72px" alt="Close"></a></div>
	    <div class="fix" style="top:5px;left:100px;">
	        <br>
	        <br>
	        <br>
	        <table>
	            <thead>
    	            <tr>
    	                <th></th>
    	                <th>Darksky</th>
    	                <th>OWM</th>
    	                <th>Domoticz</th>
    	            </tr>
    	        </thead>
    	        <tbody>
    	            <tr>
    	                <td>Temp:</td>
    	                <td>'.number_format($ds->currently->temperature, 1, ',', '').'&#8451;</td>
    	                <td>'.number_format($ow->main->temp, 1, ',', '').'&#8451;</td>
    	                <td>'.number_format($d['buiten_temp']['s'], 1, ',', '').'&#8451;</td>
    	            </tr>
    	            <tr>
    	                <td>Text:</td>
    	                <td>'.$ds->currently->summary.'</td>
    	                <td>'.$ow->weather[0]->description.'</td>
    	                <td><img src="/images/'.$d['icon']['s'].'.png"" alt="icon" id="icon"></td>
    	            </tr>
    	            <tr>
    	                <td>Wind:</td>
    	                <td>'.number_format($ds->currently->windSpeed, 1, ',', '').'</td>
    	                <td>'.number_format($ow->wind->speed, 1, ',', '').'</td>
    	                <td><img src="/images/'.$d['icon']['s'].'.png"" alt="icon" id="icon"></td>
    	            </tr>
    	        </tbody>
	        </table>
	    </div>
	    <div class="fix z1" style="top:180px;left:0px;">
	        <br>
	        <br>';
	echo 'komende 48u:';
	$argstemp['chart_div']='htemp';
	$chart=array_to_chart($htemp, $argstemp);
    echo $chart['script'];
    echo $chart['div'];
    unset($chart);
	$argsregen['chart_div']='hregen';
	$chart=array_to_chart($hregen, $argsregen);
    echo $chart['script'];
    echo $chart['div'];
    unset($chart);
    $argswind['chart_div']='hwind';
	$chart=array_to_chart($hwind, $argswind);
    echo $chart['script'];
    echo $chart['div'];
    unset($chart);
    echo '<br>komende week:';
	$argstemp['chart_div']='dtemp';
	$chart=array_to_chart($dtemp, $argstemp);
    echo $chart['script'];
    echo $chart['div'];
    unset($chart);
	$argsregen['chart_div']='dregen';
	$chart=array_to_chart($dregen, $argsregen);
    echo $chart['script'];
    echo $chart['div'];
    unset($chart);
    $argswind['chart_div']='dwind';
	$chart=array_to_chart($dwind, $argswind);
    echo $chart['script'];
    echo $chart['div'];
    unset($chart);
    echo '

	    </div>';
//	echo '<pre>';print_r($htemp);echo '</pre>';
//	echo '<pre>';print_r($hregen);echo '</pre>';
//	echo '<pre>';print_r($ds);echo '</pre>';
//	echo '<pre>';print_r($ow);echo '</pre>';
	//echo '<script>setTimeout("window.location.href=window.location.href;", 1000);</script>';
}