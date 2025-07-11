<style>pre{font-family:monospace,"courier new",courier,serif;font-size:1.0em;margin:2px;}</style>
<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
<?php
require '../secure/functions.php';
require '/var/www/authentication.php';
require 'config.php';
$day_bg_color="#222";
$today_bg_color="#555";
$weekday_bg_color="#444";
$monthname_bg_color="#aab1b8";
$link_color="#fff";
$today_day=date("j");
$today_month=date("n");
$today_year=date("Y");
$week_m0=1;
$week_d0=1;
$week_number=1;
function build_month_html($year, $month, $type)
{
	global $day_bg_color,$today_bg_color,$weekday_bg_color,$monthname_bg_color,$link_color,$today_day,$today_month,$today_year,$week_m0,$week_d0,$week_number;
	$archive_root = ARCHIVE_DIR;
	$month_name_style = "style=\"font-size: 1.0em; background-color: $monthname_bg_color;\"";
	$weekday_name_style = "style=\"font-size: 0.85em; background-color: $weekday_bg_color;\"";
	$day_style = "style='font-size: 1.0em; background-color: $day_bg_color; padding-left: 6px'";
	$today_style = "style='font-size: 1.0em; background-color: $today_bg_color; padding-left: 6px'";
	$weekday_names = array('Sun ','Mon ','Tue ','Wed ','Thu ','Fri ','Sat ');
	$timestamp = mktime(0, 0, 0, $month, 1, $year);
	$month_name = date('F', $timestamp);
	$month_days = date('t', $timestamp);				// 1 - [28-31]
	$day_of_week = date('w', $timestamp);				// 0 (Sunday) - 6 (Saturday0
	$month_label = date('M', $timestamp);
	$month_html = "<table style='border: 0px'>";
	$month_dir = str_pad($month, 2, "0", STR_PAD_LEFT);
	if (count(glob("$archive_root/$year/$month_dir/*")) == 0) $month_html .= "<caption $month_name_style >$month_name</caption><tr>";
	else $month_html .= "<caption $month_name_style><a style=\"color: $link_color;\"href='javascript:navigator_Go(\"media-archive.php?type=$type&year=$year&label=$month_name&m0=$month&d0=1&m1=$month&d1=$month_days\");'>$month_name</a></caption>";
	foreach ($weekday_names as $weekday) $month_html .= "<th $weekday_name_style ><pre>$weekday</pre></th>";
	$month_html .= "<th $weekday_name_style><pre>  </pre></th></tr><tr>";
	if ($day_of_week > 0) $month_html .= "<td $day_style colspan='$day_of_week'>&nbsp</td>";
	$n_week_links = 0;
	for ($day = 1; $day <= $month_days; $day++)	{
		$day_dir = str_pad($day, 2, "0", STR_PAD_LEFT);
		$archive_path = "$archive_root/$year/$month_dir/$day_dir";
		if (count(glob("$archive_path/videos/*")) == 0 && count(glob("$archive_path/stills/*")) == 0) $day_displayed = $day;
		else {
			$label="$month_label $day";
			$day_displayed = "<a style=\"color: $link_color;\"href='javascript:navigator_Go(\"media-archive.php?type=$type&year=$year&label=$label&m0=$month&d0=$day&m1=$month&d1=$day\");'>$day</a>";
			$n_week_links++;
		}
		if ($day == $today_day && $month == $today_month && $year == $today_year) $month_html .= "<td $today_style>$day_displayed</td>";
		else $month_html .= "<td $day_style>$day_displayed</td>";
		if ($day_of_week++ == 6) {
			$label = "Week $week_number";
			if ($n_week_links > 0) $month_html .= "<td $weekday_name_style><a style=\"color: $link_color;\"href='javascript:navigator_Go(\"media-archive.php?type=$type&year=$year&label=$label&m0=$week_m0&d0=$week_d0&m1=$month&d1=$day\");'><pre>$week_number</pre></a></td>";
			else $month_html .= "<td $weekday_name_style><pre>$week_number</pre></td>";
			if ($day < $month_days)	{
				$week_d0 = $day + 1;
				$week_m0 = $month;
				$month_html .= "</tr><tr>";
			} else {
				$week_d0 = 1;
				$week_m0 = $month + 1;
			}
			$week_number += 1;
			$n_week_links = 0;
		}
		$day_of_week %= 7;
	}
	if ($day_of_week > 0) {
		$blank_days = 7 - $day_of_week;
		$month_html .= "<td $day_style colspan='$blank_days'>&nbsp</td>";
		if ($n_week_links > 0) {
			if ($month < 12) {
				$d1 = $blank_days;
				$m1 = $month + 1;
			} else {
				$d1 = 31;
				$m1 = 12;
			}
			$month_html .= "<td $weekday_name_style><a style=\"color: $link_color;\"href=\"media-archive.php?type=$type&year=$year&m0=$week_m0&d0=$week_d0&m1=$m1&d1=$d1\"><pre>$week_number</pre></a></td>";
		} else $month_html .= "<td $weekday_name_style><pre>$week_number</pre></td>";
	}
	$month_html .= "</tr></table>";
	return $month_html;
}
$title = TITLE_STRING;
echo "<!DOCTYPE html><html><head>
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
<title>Voordeur-Oprit Calendar</title>
<link rel=\"stylesheet\" href=\"js-css/pikrellcam.css\" /><link rel=\"stylesheet\" href=\"../styles/picam1.css\"/>
</head>
<body>
<div><div class='text-center'>
<div><a class='button' style='text-decoration: none;color:#000;' href=javascript:navigator_Go('index.php');>Voordeur-Oprit</a></div></div>";
if (isset($_GET["year"])) $year = $_GET["year"];
else $year = date('Y');
if (isset($_GET["view"])) $archive_initial_view = $_GET["view"];
if (isset($_GET["next"])) $year += 1;
if (isset($_GET["prev"])) $year -= 1;
if (isset($_GET["today"])) $year = date('Y');
echo "<div class='text-center' style='padding-top: 8px;'>
<input type='image' src='images/arrow-left.png' style='margin-left:3px; vertical-align: bottom; padding-bottom: 2px' onclick='window.location=\"archive.php?year=$year&prev=prev\";'>
<span style=\"font-size: 1.5em; font-weight:500;\">$year</span>
<input type='image' src='images/arrow-right.png' style='margin-left:10px; vertical-align: bottom; padding-bottom: 2px' onclick='window.location=\"archive.php?year=$year&next=next\";'>
</div>
<div align='center'>
<table style='border: 0px solid white; border-collapse: separate; border-spacing: 14px;'>";
for ($i = 0; $i < 12; $i++)
	{
	if (($i % 3) == 0)
		echo "<tr>";
	echo "<td valign='top' style='border: 1px solid white; background-color: $day_bg_color;'>";
	echo build_month_html($year, $i + 1, $archive_initial_view);
	echo "</td>";
	if (($i % 3) == 2)
		echo "</tr>";
	}
echo "</table></div>";
if ("$archive_initial_view" == "videos") $next_view = "thumbs";
else if ("$archive_initial_view" == "thumbs") $next_view = "stills";
else if ("$archive_initial_view" == "stills") $next_view = "videos";
else $next_view = "videos";
echo "</div></body></html>";
