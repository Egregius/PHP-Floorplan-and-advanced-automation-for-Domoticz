<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$db=new mysqli('localhost', 'domotica', 'domotica');
if ($db->connect_errno>0) {
    die('Unable to connect to database ['.$db->connect_error.']');
}

$query = 'show engine innodb status;';
if (!$result = $db->query($query)) {
    die('There was an error running the query ['.$query .' - ' . $db->error . ']');
}
while ($row=$result->fetch_assoc()) {
    $status=$row['Status'];
}
$logsequence=strafter($status, 'Log sequence number ');
$logsequence=strbefore($logsequence, 'Log');
echo 'Log Sequence	'.time().'	'.$logsequence;


echo '<hr>Process list<br>';

echo '<table>
	<thead>
		<tr>
			<th>ID</th>
			<th>User</th>
			<th>Host</th>
			<th>db</th>
			<th>Command</th>
			<th>Time</th>
			<th>State</th>
			<th>Info</th>
		</tr>
	</thead>
	<tbody>';
$query = 'show full processlist;';
if (!$result = $db->query($query)) {
    die('There was an error running the query ['.$query .' - ' . $db->error . ']');
}
while ($row=$result->fetch_assoc()) {
    //print_r($row);
    echo '
		<tr>
			<td>'.$row['Id'].'</td>
			<td>'.$row['User'].'</td>
			<td>'.$row['Host'].'</td>
			<td>'.$row['db'].'</td>
			<td>'.$row['Command'].'</td>
			<td>'.$row['Time'].'</td>
			<td>'.$row['State'].'</td>
			<td>'.$row['Info'].'</td>
		</tr>';
}
echo '
	</tbody>
</table><hr>';
echo '<pre>'.$status;
function strafter($string,$substring)
{
    $pos=strpos($string, $substring);
    if ($pos===false) {
        return $string;
    } else {
        return(substr($string, $pos+strlen($substring)));
    }
}
function strbefore($string,$substring)
{
    $pos=strpos($string, $substring);
    if ($pos===false) {
        return $string;
    } else {
        return(substr($string, 0, $pos));
    }
}
