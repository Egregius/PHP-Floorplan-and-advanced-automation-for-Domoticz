<?php
$start=microtime(true);
require('/var/www/config.php');
if(isset($_POST['Remove'])){
	if(isset($_POST['name'])){
		apcu_delete($_POST['name']);
	}
}elseif(isset($_POST['Update'])){
	if(isset($_POST['name'])){
		apcu_store($_POST['name'],$_POST['Update']);
	}
}
?>
<link rel="stylesheet" type="text/css" href="apcu.css">
<script type="text/javascript" language="javascript" src="/scripts/jquery-3.5.1.min.js"></script>
<script type="text/javascript" language="javascript" src="/scripts/datatables-1.10.22.min.js"></script>
<script type="text/javascript" language="javascript" src="/scripts/jQuery.dataTables.columnFilter.js"></script>
<script type="text/javascript" language="javascript" src="/scripts/jQuery.datesort.js"></script>
<script type="text/javascript" charset="utf-8">
var asInitVals = new Array();
	$(document).ready(function() {
		var Table = $('#Table').DataTable(
		{
			"paging": false,
			"scrollY": 1020,
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
<div style="top:0px;width:400px;margin:0 auto"><a href="apcu2.php" class="btn" style="width:400px;padding:18px;">Refresh</a></div>
<?php
$apcu=apcu_cache_info();
$running=time()-$apcu['start_time'];
$days=strftime("%j",$running)-1;
$time=strftime("%H:%M:%S",$running-3600);
?>
<style>
table{width:100%;}
table.pretty{width:100%;}
th{font-weight:bold;}
td{padding:2px 10px;}
</style>
<div style="width:400px;">
<table>
	<tr><th align="right">Start time</td><td align="right"><?php echo strftime("%Y-%m-%d %H:%M:%S",$apcu['start_time']);?></td></tr>
	<tr><th align="right">Run time</th><td align="right"><?php echo $days.' '.$time; ?></td></tr>
	<tr><th align="right">entries</th><td align="right"><?php echo $apcu['num_entries'];?></td></tr>
	<tr><th align="right">misses</th><td align="right"><?php echo $apcu['num_misses'];?></td><td align="right"><?php echo round($apcu['num_misses']/$running,2);?>/sec</td></tr>
	<tr><th align="right">updates</th><td align="right"><?php echo $apcu['num_inserts'];?></td><td align="right"><?php echo round($apcu['num_inserts']/$running,2);?>/sec</td></tr>
	<tr><th align="right">hits</th><td align="right"><?php echo $apcu['num_hits'];?></td><td align="right"><?php echo round($apcu['num_hits']/$running,2);?>/sec</td></tr>
</table>
</div>
<?php //print_r($apcu);?>
<div id="dataTables_wrapper" style="width:1100px;margin:0 auto;">
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
				<td align="center">'.strftime("%Y-%m-%d %H:%M:%S",$c['creation_time']).'</td>
				<td>
					<form method="POST">
						<input type="hidden" name="name" value="'.$c['info'].'"/>
						<input type="text" name="Update" value="'.$value.'" onchange="submit.thisform()"/>
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