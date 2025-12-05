<?php
$start=microtime(true);
if(isset($_POST['Update'])){
	if(isset($_POST['name'])){
		apcu_store($_POST['name'],$_POST['Update']);
	}
}elseif(isset($_POST['Remove'])){
	apcu_delete($_POST['name']);
}
?>
<head>
<title>apcu Domotica Server</title>
<link rel="stylesheet" type="text/css" href="apcu.css">
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://urbexforum.org/Themes/default/scripts/js/jQuery.dataTables.columnFilter.js"></script>
<script type="text/javascript" language="javascript" src="https://urbexforum.org/Themes/default/scripts/js/jQuery.datesort.js"></script>
<script type="text/javascript" charset="utf-8">
var asInitVals = new Array();
	$(document).ready(function() {
		var Table = $('#Table').DataTable(
		{
			"paging": false,
			"scrollY": 890,
			"stateSave": true,
			"order": [[ 2, 'desc' ]],
		});
		$('#search').keyup(function ()
		{
			Table.fnFilter(this.value);
		});
	});
function ConfirmDelete(){return confirm('Are you sure?');}
</script>
<div style="top:0px;width:400px;margin:0 auto"><a href="apcu.php" class="btn" style="width:400px;padding:18px;">Refresh</a></div>
<?php
$apcu=apcu_cache_info();
$running=time()-$apcu['start_time'];
$days = floor($running / 86400);
$time = gmdate("H:i:s", $running);
?>
<style>
table{width:100%;}
table.pretty{width:100%;}
th{font-weight:bold;}
td{padding:2px 10px;}
</style>
<div style="width:400px;">
<table>
	<tr><th align="right">Start time</td><td align="right"><?php echo date("Y-m-d G:i:s",$apcu['start_time']);?></td></tr>
	<tr><th align="right">Run time</th><td align="right"><?php echo $days.' '.$time.' - '.$running; ?></td></tr>
	<tr><th align="right">entries</th><td align="right"><?php echo $apcu['num_entries'];?></td></tr>
	<tr><th align="right">misses</th><td align="right"><?php echo $apcu['num_misses'];?></td><td align="right"><?php echo round($apcu['num_misses']/$running,2);?>/sec</td></tr>
	<tr><th align="right">updates</th><td align="right"><?php echo $apcu['num_inserts'];?></td><td align="right"><?php echo round($apcu['num_inserts']/$running,2);?>/sec</td></tr>
	<tr><th align="right">hits</th><td align="right"><?php echo $apcu['num_hits'];?></td><td align="right"><?php echo round($apcu['num_hits']/$running,2);?>/sec</td></tr>
</table>
</div>
<?php //print_r($apcu);?>
<div id="dataTables_wrapper" style="width:1400px;margin:0 auto;">
<table id="Table" class="pretty" BORDER="1" CELLPADDING="3" CELLSPACING="0">
	<thead>
		<tr>
			<th width="50px">Name</th>
			<th width="100px">Value</th>
			<th width="15px">Hits</th>
			<th width="35px">Created</th>
			<th width="45px">Update</th>
			<th width="26px">Delete</th>
		</tr>
	</thead>
<tbody>
<?php

foreach($apcu['cache_list'] as $c){
		$value=apcu_fetch($c['info']);
		echo '<tr>
				<td>'.$c['info'].'</td>
				<td style="word-wrap: break-word;">'.$value.'</td>
				<td align="right">'.$c['num_hits'].'</td>
				<td align="center">'.date("Y-m-d G:i:s",$c['creation_time']).'</td>
				<td>
					<form method="POST">
						<input type="hidden" name="name" value="'.$c['info'].'"/>
						<textarea name="Update" >'.$value.'</textarea>
						<input type="submit" name="Save" value="S" class="btn"/>
					</form>
				</td>
				<td>
					<form method="POST">
						<input type="hidden" name="name" value="'.$c['info'].'"/>
						<input type="submit" name="Remove" value="Remove" class="btn"/>
					</form>
				</td>
			</tr>';
}
echo '
</tbody></table></div>
'.number_format(((microtime(true)-$start)*1000),3);
