<script>
	function scroll_to_selected() {document.getElementById("selected").scrollIntoView(true);}
	function select_all(source)	{
		checkboxes = document.getElementsByName('file_list[]');
		for (var c in checkboxes) {checkboxes[c].checked = source.checked;}
		checkboxlist = document.getElementsByName('checkbox_list[]');
		for (var c in checkboxlist)	{checkboxlist[c].checked = source.checked;}
	}
	function select_day(source, ymd){
		checkboxes = document.getElementsByName('file_list[]');
		for (var c in checkboxes) {
			var val = checkboxes[c].value;
			if (val.substring(0, 10)==ymd)
				checkboxes[c].checked = source.checked;
			}
		}
	function navigator_Go(url) {window.location.assign(url);}
</script>
<style type="text/css">
	a.anchor {display: block; position: relative; top: -250px; visibility: hidden;}
</style>
<?php
require '../secure/functions.php';
require '/var/www/authentication.php';
require 'config.php';
if($home===true) {
function eng_filesize($bytes, $decimals = 1)
{
	$sz='BKMGTP';
	$factor=floor((strlen($bytes)-1)/3);
	return sprintf("%.{$decimals}f", $bytes / pow(1000, $factor)) . @$sz[$factor];
}
function media_dir_array_create($media_dir)
{
	global	$archive_root, $media_mode, $media_type, $media_subdir;
	$media_array = array();
	$file_dir = "$media_dir/$media_subdir";
	if (is_dir($file_dir)) {
		$tmp_array = array_slice(scandir($file_dir), 2);
		$n_files = count($tmp_array);
		foreach($tmp_array as $file_name) {
			$extension = substr(strrchr($file_name, "."), 0);
			if ("$extension" != ".mp4"&&"$extension" != ".jpg"&&"$extension" != ".h264") continue;
			$mtime = 0;
			$parts = explode("_", $file_name);
			if (count($parts)==4) {
				$date = explode("-", $parts[1]);
				if (count($date)==3) {
					$parts[2] = str_replace(':', '.', $parts[2]);
					$time = explode(".", $parts[2]);
					$n = count($time);
					if ($n==3) $mtime = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
					else if ($n==2) $mtime = mktime($time[0], $time[1], 0, $date[1], $date[2], $date[0]);
				}
			}
			if ($mtime==0) $mtime = filemtime("$file_dir" . "/" . "$file_name");
			$ymd = date("Y-m-d", $mtime);
			if ("$media_type"=="videos") {
				if ("$media_subdir"=="videos") {
					$thumb_name = str_replace(".mp4", ".th.jpg", $file_name);
					$short_name = date('H:i:s', $mtime) . "$extension";
				} else {
					$thumb_name = $file_name;
					$file_name = str_replace(".th.jpg", ".mp4", $thumb_name);
					$short_name = date('H:i:s', $mtime);
				}
				$media_array[] = array('file_name' => $file_name,
								'media_dir'		=> "$media_dir",
								'file_path'		=> "$media_dir" . "/videos/" . "$file_name",
								'thumb_path'	   => "$media_dir" . "/thumbs/" . "$thumb_name",
								'mtime'			=> $mtime,
								'date'			 => $ymd,
								'short_name'	   => $short_name);
				} else {
					$short_name = date('H:i:s', $mtime) . "$extension";
					$media_array[] = array('file_name' => $file_name,
									'media_dir'		=> "$media_dir",
									'file_path'		=> "$media_dir" . "/stills/" . "$file_name",
									'mtime'			=> $mtime,
									'date'			 => $ymd,
									'short_name'	   => $short_name);
				}
			}
		}
	return $media_array;
	}
function archive_media_dir($year, $month, $day)	{
	global	$archive_root;
	$month_dir = str_pad($month, 2, "0", STR_PAD_LEFT);
	$day_dir = str_pad($day, 2, "0", STR_PAD_LEFT);
	return "$archive_root/$year/$month_dir/$day_dir";
}
function media_array_create() {
	global	$media_array, $media_array_size, $media_mode, $media_dir;
	global	$year, $month0, $day0, $month1, $day1;
	$media_array = array();
	if ("$media_mode"=="archive") {
		if ($month0==$month1)	$dlast = $day1;
		else $dlast = 31;
		for ($day = $day0; $day <= $dlast; $day++) {
			$dir = archive_media_dir($year, $month0, $day);
			$media_array = array_merge($media_array, media_dir_array_create($dir));
		}
		if ($month1 > $month0)
			for ($day = 1; $day <= $day1; $day++) {
				$dir = archive_media_dir($year, $month1, $day);
				$media_array = array_merge($media_array, media_dir_array_create($dir));
			}
		}
	else $media_array = media_dir_array_create($media_dir);
	usort($media_array, function($a, $b) {return strcmp($a["mtime"], $b["mtime"]);});
	krsort($media_array);
	$media_array = array_values($media_array);
	$media_array_size = count($media_array);
}
function media_array_index($name) {
	global	$media_array, $media_array_size;
	if ($media_array_size==0) return -1;
	else if ("$name"=="") return 0;
	for ($i = 0; $i < $media_array_size; $i++)
		if ("$name"==$media_array[$i]['file_name']) return $i;
	return 0;
}
function delete_file($media_dir, $fname) {
	global	$media_mode, $media_subdir;
	if (!is_dir($media_dir)) return;
	if ("$media_subdir"=="stills") unlink("$media_dir/stills/$fname");
	else {
		unlink("$media_dir/videos/$fname");
		$thumb = str_replace(".mp4", ".th.jpg", $fname);
		unlink("$media_dir/thumbs/$thumb");
		$h264 = "$media_dir/videos/$fname" . ".h264";
		if (is_file("$h264")) unlink("$h264");
		$csv = "$media_dir/videos/". str_replace(".mp4", ".csv", $fname);
		if (is_file("$csv")) unlink("$csv");
	}
	if ("$media_mode"=="archive")	delete_empty_media_dir($media_dir);
}
function delete_day($media_dir, $ymd) {
	global	$media_mode, $media_type;
	if (!is_dir($media_dir)) return;
	if ("$media_type"=="videos") {
		array_map('unlink', glob("$media_dir/videos/*$ymd*.mp4"));
		array_map('unlink', glob("$media_dir/videos/*$ymd*.csv"));
		array_map('unlink', glob("$media_dir/videos/*$ymd*.h264"));
		array_map('unlink', glob("$media_dir/thumbs/*$ymd*.th.jpg"));
	} else if ("$media_type"=="stills") array_map('unlink', glob("$media_dir/stills/*$ymd*.jpg"));
	if ("$media_mode"=="archive")	delete_empty_media_dir($media_dir);
}
function delete_all_files($media_dir) {
	global	$media_type;
	if (!is_dir($media_dir)) return;
	if ("$media_type"=="videos") {
		array_map('unlink', glob("$media_dir/videos/*.mp4"));
		array_map('unlink', glob("$media_dir/videos/*.csv"));
		array_map('unlink', glob("$media_dir/videos/*.h264"));
		array_map('unlink', glob("$media_dir/thumbs/*.th.jpg"));
	} else if ("$media_type"=="stills") array_map('unlink', glob("$media_dir/stills/*.jpg"));
}
function delete_empty_media_dir($media_dir) {
	if (!is_dir($media_dir)) return;
	$subdir = "$media_dir/videos";
	if (is_dir($subdir)&&count(glob("$subdir/*"))==0) rmdir($subdir);
	$subdir = "$media_dir/thumbs";
	if (is_dir($subdir)&&count(glob("$subdir/*"))==0) rmdir($subdir);
	$subdir = "$media_dir/stills";
	if (is_dir($subdir)&&count(glob("$subdir/*"))==0) rmdir($subdir);
	if (count(glob("$media_dir/*"))==0) rmdir($media_dir);
}
function delete_archive_range($year, $month0, $day0, $month1, $day1)
	{
	global	$archive_root, $media_mode;

	if ("$media_mode" != "archive")
		return;

	if ($month0==$month1)
		$dlast = $day1;
	else
		$dlast = 31;

	$month_dir = str_pad($month0, 2, "0", STR_PAD_LEFT);
	for ($day = $day0; $day <= $dlast; $day++)
		{
		$day_dir = str_pad($day, 2, "0", STR_PAD_LEFT);
		$del_dir = "$archive_root/$year/$month_dir/$day_dir";
		delete_day($del_dir, "$year-$month_dir-$day_dir");
		delete_empty_media_dir("$del_dir");
		delete_empty_media_dir("$archive_root/$month_dir");
		}
	if ($month1 > $month0)
		{
		$month_dir = str_pad($month1, 2, "0", STR_PAD_LEFT);
		for ($day = 1; $day <= $day1; $day++)
			{
			$day_dir = str_pad($day, 2, "0", STR_PAD_LEFT);
			$del_dir = "$archive_root/$year/$month_dir/$day_dir";
			delete_day($del_dir, "$year-$month_dir-$day_dir");
			delete_empty_media_dir("$del_dir");
			delete_empty_media_dir("$archive_root/$month_dir");
			}
		}
	}
function wait_files_gone($key, $pat)
	{
	global $media_dir, $media_type;

	for ($i = 0; $i < 16; ++$i)
		{
		usleep(100000);
		if ("$key"=="file"&&!file_exists($pat))
			break;
		else if ("$key"=="day")
			{
			if ("$media_type"=="videos")
				{
				if (   count(glob("$media_dir/videos/*$pat*"))==0
				   &&count(glob("$media_dir/thumbs/*$pat*"))==0
				   )
					break;
				}
			else if ("$media_type"=="stills")
				{
				if (count(glob("$media_dir/stills/*$pat*"))==0)
					break;
				}
			}
			break;
		}
	usleep(400000);
	if ($i==16)
		echo "<script type='text/javascript'>alert('Archive may have failed. Is pikrellcam running?');</script>";
	}

function restart_page($selected)
	{
	global $env, $name_style;

	echo "</body></html>";
	if ("$selected"=="")
		echo "<script>window.location=\"media-archive.php?$env\";</script>";
	else
		echo "<script>window.location=\"media-archive.php?$env&file=$selected\";</script>";
	exit(0);
	}


	$media_mode = "archive";
	if (isset($_GET["mode"]))
		$media_mode = $_GET["mode"];
	$title = TITLE_STRING;

	$header = "<!DOCTYPE html><html><head>";
	$header .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
	if ("$media_mode"=="archive")
		$header .= "<title>Voordeur-Oprit Archive</title>";
	else
		$header .= "<title>Voordeur-Oprit Media</title>";
	$header .= "<link rel=\"stylesheet\" href=\"/styles/picamarchief.css\"/>";
	$header .= "</head>";
	$header .= "<body>";
	echo $header;

	echo '
	<div><div class="menu">
	<div style="margin: auto; overflow: visible;">
	<div class="marginright">
	<form method="POST" action="/floorplan.php"><input type="submit" value="Plan" class="btn" style="min-width:5em"/></form>
	<form method="POST" action="index.php"><input type="submit" value="Live" class="btn" style="min-width:5em"/></form>
	<form method="POST" action="media-archive.php?type=videos&year='.date("Y").'&label= &m0='.date("n",time()-86400).'&d0='.date("j",time()-86400).'&m1='.date("n").'&d1='.date("j").'">
		<input type="submit" value="Archief" class="btn" style="min-width:5em"/>
	</form>
	<form method="POST" action="archive.php"><input type="submit" value="Calendar" class="btn" style="min-width:5em"/></form>';
	$archive_root = ARCHIVE_DIR;

	$media_type = "videos";
	$selected = "";
	$media_dir = "";
	$prev_index = 0;
	$label = "??";
	$env = "";
	$year = "";

	if (isset($_GET["newtype"])) $media_type = $_GET["newtype"];
	else if (isset($_GET["type"])) $media_type = $_GET["type"];
	if ("$media_type"=="thumbs") $media_type = "videos";
	if (isset($_GET["label"])) $label = $_GET["label"];
	if (isset($_GET["file"])) $selected = $_GET["file"];
	if (isset($_GET["dir"])) $media_dir = $_GET["dir"];
	if ("$media_mode"=="archive")	{
		$year = $_REQUEST["year"];
		$month0 = $_REQUEST["m0"];
		$day0 = $_REQUEST["d0"];
		$month1 = $_REQUEST["m1"];
		$day1 = $_REQUEST["d1"];
		$env = "mode=$media_mode&type=$media_type&label=$label&year=$year&m0=$month0&d0=$day0&m1=$month1&d1=$day1";
	} else {
		$media_dir = "media";
		$env = "mode=$media_mode&type=$media_type";
	}
	if (isset($_GET["videos_mode_list"])) $videos_mode = "list";
	if (isset($_GET["videos_mode_thumbs"]))	$videos_mode = "thumbs";
	if ("$media_type"=="stills") $media_subdir = "stills";
	else if ("$videos_mode"=="thumbs") $media_subdir = "thumbs";
	else $media_subdir = "videos";
	if (isset($_GET["toggle_scroll"])) {
		if ("$media_mode"=="archive") {
			if ("$archive_thumbs_scrolled"=="yes") $archive_thumbs_scrolled = "no";
			else $archive_thumbs_scrolled = "yes";
		} else {
			if ("$media_thumbs_scrolled"=="yes")	$media_thumbs_scrolled = "no";
			else $media_thumbs_scrolled = "yes";
		}
	}
	if (isset($_GET["toggle_name_style"])) {
		if ("$name_style"=="short") $name_style = "full";
		else $name_style = "short";
	}
	if (isset($_GET["inc_columns"])) {
		if ($n_columns < 10) $n_columns += 1;;
	}
	if (isset($_GET["dec_columns"])) {
		if ($n_columns > 2)	$n_columns -= 1;;
	}
	if (isset($_POST['action'])&&!empty($_POST['file_list']))	{
		$action = $_POST['action'];
		foreach ($_POST['file_list'] as $file) {
			$parts = explode("/", $file);
			$ymd = $parts[0];
			$date = explode("-", $ymd);
			$vid = $parts[1];
			if ($action=="delete_selected") {
				if ("$media_mode"=="archive")	$media_dir = archive_media_dir($date[0], $date[1], $date[2]);
				delete_file($media_dir, $vid);
			} else if ($action=="archive_selected") {
				$fifo = fopen(FIFO_FILE,"w");
				fwrite($fifo, "archive_video $vid $ymd");
				fclose($fifo);
				wait_files_gone("file", "$media_dir/videos/$vid");
			}
		}
		restart_page($selected);
	}
	if (isset($_GET["delete"]))	{
		$del_name = $_GET["delete"];
		delete_file($media_dir, $del_name);
		restart_page($selected);
	}
	if (isset($_GET["delete_day"]))	{
		$ymd = $_GET["delete_day"];
		delete_day($media_dir, $ymd);
		restart_page($selected);
	}
	if (isset($_GET["delete_all"]))	{
		if ("$media_mode"=="archive")	delete_archive_range($year, $month0, $day0, $month1, $day1);
		else delete_all_files($media_dir);
		restart_page("");
	}
	media_array_create();
	$index = media_array_index("$selected");
	if("$media_subdir"=="thumbs"&&(("$media_mode"=="archive"&&"$archive_thumbs_scrolled"=="no")||("$media_mode"=="media"&&"$media_thumbs_scrolled"=="no")))	$scrolled = "no";
	else $scrolled = "yes";
	if ($index >= 0) {
		$file_path = $media_array[$index]['file_path'];
		$file_name = $media_array[$index]['file_name'];
		if ("$media_type"=="stills") echo "<a href=$file_path target='_blank'>
					<img src=\"$file_path\"
					class='thumbimage'>
				  </a>";
		else if ("$scrolled"=="yes") {
			$thumb_path = $media_array[$index]['thumb_path'];
			echo "<div style='width:100%; max-width:960px'><video controls width='100%'><source src=\"$file_path\" type='video/mp4'>Your browser does not support the video tag.</video></div>";
			if (is_file($thumb_path)) echo "<img src=\"$thumb_path\" class='thumbimage'>";
			else echo "<img src=\"$background_image\" class='thumbimage'>";
		}
		if ("$scrolled"=="yes")	{
			echo "<div style='margin: auto; overflow: visible;'>";
			echo   "<div style='margin-right:6px; margin-top: 6px'>";
			echo "<selected>&nbsp; $file_name</selected>";

			$wopen = "download.php?file=$file_path";
			if($udevice=='Mac') echo "<input type='button' value='Download'	class='btn'	style='margin-left: 16px;' onclick='window.open(\"$wopen\", \"_blank\");'>";
			$media_dir = $media_array[$index]['media_dir'];
			$next_select='';
			if ($index > 0)	$next_file = "&file=$next_select";
			else $next_file = "";
			$left_margin = 100;
			if ("$media_mode" != "archive")	{
				$ymd = $media_array[$index]['date'];
				echo "<input type='button' value='Archive'	class='btn'	style='margin-left: 100px;'	onclick='window.location=\"media-archive.php?$env&date=$ymd&dir=$media_dir&archive=$file_name$next_file\";'>";
				$left_margin = 10;
				}
			echo "<input type='button' value='Delete' class='btn alert-control'	style=\"margin-left: $left_margin;\" onclick='window.location=\"media-archive.php?$env&dir=$media_dir&delete=$file_name$next_file\";'>";
			echo "</div>";
			echo "</div>";
			}
		}
	else echo "<p style='margin-top:20px; margin-bottom:20px;'><h4>------</h4></p>";
	echo "</div>";
	if ($media_array_size==0) echo "<p>No files.</p>";
	else {
		$ymd_header = "";
		$div_style = "margin-top:100px";
		if ("$media_subdir"=="thumbs") echo "<form method=\"POST\" action=\"media-archive.php?$env\">";
		echo "<div style=\"$div_style\">";
		if ("$scrolled"=="yes")	echo "<table width='100%' cellpadding='2'>";
		else echo "<table width='100%' cellpadding='2' frame='box'>";
		$next_select = "";
		for ($k = 0; $k < $media_array_size; $k = $last){
			$ymd = $media_array[$k]['date'];
			if ($k > 0)	$next_select = $media_array[$k - 1]['file_name'];	// look back one
			if ("$ymd_header" != "$ymd"){
				echo "<td style='vertical-align: bottom; padding-bottom:6px; padding-top:18px'>";
				$date_string = date('D - M j Y', $media_array[$k]['mtime']);
				echo "<span style='margin-left: 4px; font-size: 1.0em; font-weight: 500;'>$date_string</span>";
				$ymd_header = $ymd;
				$dir = $media_array[$k]['media_dir'];
				if ($n_columns > 2&&"$media_subdir" != "thumbs") echo "</td><td>";
				if ("$next_select" != "") $next_file = "&file=$next_select";
				else $next_file = "";
				if ("$media_subdir"=="thumbs") if($udevice=='Mac') echo "<input style='margin-left: 16px' type='checkbox' name='checkbox_list[]' onClick=\"select_day(this, '$ymd')\"/>";
				else {
					if ("$media_mode" != "archive") {
						echo "<input type='button' value='Archive Day' class='btn' style='margin-left: 32px; margin-bottom:4px; margin-top:24px; font-size: 0.82em; text-align: left;' onclick='if (confirm(\"Archive day $ymd?\")){window.location=\"media-archive.php?$env&dir=$dir&archive_date=$ymd$next_file\";}'>";
						if ($n_columns > 2&&"$media_subdir" != "thumbs") echo "</td><td>";
					}
					if($udevice=='Mac') echo "<input type='button' value='Delete Day' class='btn alert-control'	style='margin-left: 32px; margin-bottom:4px; margin-top:24px; font-size: 0.82em; text-align: left;' onclick='if (confirm(\"Delete day $ymd?\")) {window.location=\"media-archive.php?$env&dir=$dir&delete_day=$ymd$next_file\";}'>";
				}
				echo "</td>";
				for ($last = $k; $last < $media_array_size&&$media_array[$last]['date']==$ymd; ++$last)
					;
				$n_rows = ceil(($last - $k) / $n_columns);
			}
			if ("$media_subdir"=="thumbs"){
				echo "<tr><td>";
				for ($idx = $k; $idx < $last; ++$idx){
					$thumb_path = $media_array[$idx]['thumb_path'];
					$fname = $media_array[$idx]['file_name'];
					$path = $media_array[$idx]['file_path'];
					$fsize = eng_filesize(filesize("$path"));
					$display_name = $media_array[$idx]['short_name'];
					$color = $media_text_color;
					$border_color = "";
					if ("$scrolled"=="yes"&&$fname==$media_array[$index]['file_name']){
						$color = $selected_text_color;
						$border_color = "border-color: $selected_text_color;";
						echo "<a id='selected' class='anchor' style='display:inline'></a>";
					}
					$out = "<fieldset class='thumb'>";
					$out .= "<span>$display_name &nbsp</span>";
					if($udevice=='Mac') $out .= "<span style='float:right'><input type='checkbox' name='file_list[]' value=\"$ymd/$fname\"/></span>";
					$out .= "<span style='float:right;'>$fsize</span><br>";
					if (substr($fname, 0, 3)=="man") $out .= "<span>Manual</span><br>";
					if (substr($fname, 0, 2)=="tl") {
						$period = "---";
						$parts = explode("_", $fname);
						if (count($parts)==4)	{
							$tail = explode(".", $parts[3]);
							if (count($tail)==2) $period = $tail[0];
						}
						if (substr($period, 0, 1)=="0") $period = "---";
						$out .= "<span>Timelapse: $period</span><br>";
					}
					echo "$out";
					//if ("$scrolled"=="yes") {
						echo "<a href='javascript:navigator_Go(\"media-archive.php?$env&file=$fname\");'>";
						echo "<img src=\"$thumb_path\" class='thumbimage'/></a></fieldset>";
					//} else {
					//	if ("$video_url"=="") echo "<a href='javascript:navigator_Go(\"$path\");'>";
					//	else echo "<a href='javascript:navigator_Go(\"$video_url$fname\");'>";
					//	echo "<img src=\"$thumb_path\" style='padding:1px 1px 2px 1px'/></a></fieldset>";
					//}
				}
				echo "</td></tr>";
			} else {
				for ($row = 0; $row < $n_rows; ++$row) {
					echo "<tr>";
					for ($col = 0; $col < $n_columns; ++$col) {
						echo "<td style='font-size: 0.92em;'>";
						$idx = $k + $row + $col * $n_rows;
						if ($idx < $last) {
							$fname = $media_array[$idx]['file_name'];
							$path = $media_array[$idx]['file_path'];
							$fsize = eng_filesize(filesize("$path"));
							if ($name_style=="short") $display_name = $media_array[$idx]['short_name'];
							else $display_name = $fname;
							$border_color = "";
							if ($fname==$media_array[$index]['file_name']) {
								$color = $selected_text_color;
								echo "<a id='selected' class='anchor'></a>";
							} else if (substr($fname, 0, 3)=="man") $color = $manual_video_text_color;
							else $color = $media_text_color;
							echo "<a href='media-archive.php?$env&file=$fname' style=\"text-decoration: none;\">$display_name</a>
								<span style='font-size: 0.86em;'>($fsize)</span>";
						} else	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "</td>";
					}
					echo "</tr>";
				}
			}
		}
		echo "</table>";
		echo "</div>";
	}
	if ("$media_subdir"=="thumbs") {
		echo "<span style='margin-left: 50px;'>Selections:</span>";
		if ("$media_mode" != "archive")	echo "<button type='submit' class='btn' style='margin-left: 8px'; value='archive_selected' name='action' onclick=\"return confirm('Archive selected thumbs/videos?');\">Archive</button>";
		if($udevice=='Mac') echo "<button type='submit' class='btn alert-control' style='margin-left: 8px' value='delete_selected' name='action' onclick=\"return confirm('Delete selected thumbs/videos?');\">Delete</button>";
		echo "<span style='float:right;margin-right:20px;'>";
		echo "Select All";
		echo "<input style='margin-right:16px;' type='checkbox' onClick='select_all(this)'/>";
		echo "Files:&thinsp;$media_array_size";
	$disk_total = disk_total_space($archive_root);
	$disk_free = disk_free_space($archive_root);
	$free_percent = sprintf('%.1f',($disk_free / $disk_total) * 100);
	$total = eng_filesize($disk_total);
	$free = eng_filesize($disk_free);
	echo "<span style=\"float: top; margin-left:10px;\">Disk free:&thinsp;${free}B&thinsp;($free_percent %)</span>";
	echo "</span>";
		}
	else {
		echo "<span style='margin-left: 70px;'>
		<input type='button' value='Delete All' class='btn alert-control' style='margin-right:40px;' onclick='if (confirm(\"Delete all $year $label?\")) {window.location=\"media-archive.php?$env&delete_all\";}'>
		</span>
		<span style='float:right;'>Files:&thinsp;$media_array_size
		<a style='margin-left: 32px; margin-right:24px;' href='media-archive.php?$env&toggle_name_style'>
			Name Style</a>";
		echo "&nbsp;&nbsp;Columns $n_columns:";
		if ($n_columns > 2)	echo "<input type='button' value='-' class='btn' style='margin-left:6px;' onclick='window.location=\"media-archive.php?$env&dec_columns\";'>";
		if ($n_columns < 10) echo "<input type='button' value='+' class='btn' style='margin-left:6px;' onclick='window.location=\"media-archive.php?$env&inc_columns\";'>";
		echo "</span>";
	}
	echo "</span></div>";
	if ("$media_subdir"=="thumbs") echo "</form>";
	echo "</div></body></html>";
} else {header("Location: ../index.php");die("Redirecting to: ../index.php");}
