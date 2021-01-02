<?php
/**
 * Pass2PHP Temperature Control
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/

include('_TC_heating.php');

foreach (array('living', 'kamer', 'alex') as $k) {
	${'dif'.$k}=number_format($d[$k.'_temp']['s']-$d[$k.'_set']['s'], 1);
	if (${'dif'.$k}<$bigdif) $bigdif=${'dif'.$k};
	$daikin=json_decode($d['daikin'.$k]['s']);

//	lg($k.' corr='.$corr.' set='.$set.' temp='.$d[$k.'_temp']['s']);
	if ($d[$k.'_set']['s']>22) $d[$k.'_set']['s']=22;
	if ($d[$k.'_set']['s']>10) {
		if (${'dif'.$k}>=0.3)		{$power=0;$set=$d[$k.'_set']['s']+0;}
		elseif (${'dif'.$k}>=0.2)	{$power=1;$set=$d[$k.'_set']['s']+0.5;}
		elseif (${'dif'.$k}>=0.1)	{$power=1;$set=$d[$k.'_set']['s']+1;}
		elseif (${'dif'.$k}>=0)	{$power=1;$set=$d[$k.'_set']['s']+1.5;}
		elseif (${'dif'.$k}>=-0.1)	{$power=1;$set=$d[$k.'_set']['s']+2;}
		elseif (${'dif'.$k}>=-0.2)	{$power=1;$set=$d[$k.'_set']['s']+2;}
		elseif (${'dif'.$k}>=-0.3)	{$power=1;$set=$d[$k.'_set']['s']+2;}
		elseif (${'dif'.$k}>=-0.4)	{$power=1;$set=$d[$k.'_set']['s']+2;}
		elseif (${'dif'.$k}>=-0.5)	{$power=1;$set=$d[$k.'_set']['s']+2;}
		elseif (${'dif'.$k}>=-0.6)	{$power=1;$set=$d[$k.'_set']['s']+2;}
		elseif (${'dif'.$k}>=-0.7)	{$power=1;$set=$d[$k.'_set']['s']+2;}
		elseif (${'dif'.$k}>=-0.8)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-0.9)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-1.0)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-1.1)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-1.2)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-1.3)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-1.4)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-1.5)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		else {$power=1;$set=$d[$k.'_set']['s']+4;}
		$rate='A';
		if ($k=='living') $set=$set+0.5;
		elseif ($k=='kamer') {
			$set=$set-2;
			if (TIME<strtotime('8:30')||TIME>strtotime('22:30'))$rate='B';
		}
		elseif ($k=='alex') {
			$set=$d[$k.'_set']['s']-2;
			if (TIME<strtotime('8:30')||TIME>strtotime('19:30'))$rate='B';
		}
		$set=ceil($set * 2) / 2;
		if ($set>25) $set=25;
		elseif ($set<10) $set=10;
		if ($daikin->stemp!=$set||$daikin->pow!=$power||$daikin->mode!=4||$daikin->f_rate!=$rate) {
			daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $rate);
			$data=json_decode($d[$k.'_set']['icon'], true);
			$data['power']=$power;
			$data['mode']=4;
			$data['fan']=$rate;
			$data['set']=$set;
			storeicon($k.'_set', json_encode($data));
		}
	} else {
		if ($daikin->pow!=0||$daikin->mode!=4) {
			daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__);
			$data=json_decode($d[$k.'_set']['icon'], true);
			$data['power']=$power;
			$data['mode']=4;
			$data['fan']=$rate;
			$data['set']=$set;
			storeicon($k.'_set', json_encode($data));
		}
	}
}

//lg('bigdif='.$bigdif.'|brander='.$d['brander']['s'].'|timebrander='.past('brander'));
if ($d['brander']['s']=='On'&&past('brander')>420) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);

if ($bigdif!=$d['bigdif']['m']) storemode('bigdif', $bigdif, basename(__FILE__).':'.__LINE__);

include('_TC_heating_badk-zolder.php');

/**
 * Function setradiator: calculates the setpoint for the Danfoss thermostat valve
 *
 * @param string  $name   Not used anymore
 * @param int	 $dif	Difference in temperature
 * @param boolean $koudst Is it the coldest room of all?
 * @param int	 $set	default setpoint
 *
 * @return null
 */
/*function setradiator($name,$dif,$koudst=false,$set=14)
{
	if ($koudst==true) $setpoint=28;
	else $setpoint=$set-ceil($dif*4);
	if ($setpoint>28) $setpoint=28;
	elseif ($setpoint<4) $setpoint=4;
	return round($setpoint, 0);
}*/

include('_Rolluiken_Heating.php');
