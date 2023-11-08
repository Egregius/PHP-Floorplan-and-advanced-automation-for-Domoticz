<?php
require 'secure/functions.php';
require '/var/www/authentication.php';
$tail=new PHPTail(
	array(
	"Domoticz"=>"/temp/domoticz.log",
	)
);
if (isset($_GET['ajax'])) {
	$vandaag=strftime("%F",$_SERVER['REQUEST_TIME']);
	echo str_replace($vandaag.' ','',$tail->getNewLines($_GET['file'], $_GET['lastsize']));die();
}
$tail->generateGUI();
class PHPTail {
	private $log="";
	private $updateTime;
	public function __construct($log) {
		$this->log=is_array($log)?$log:array($log);
	}
	public function getNewLines($file,$lastFetchedSize) {
		clearstatcache();
		if (empty($file)) $file=key(array_slice($this->log, 0, 1, true));
		$fsize=filesize($this->log[$file]);
		$maxLength=($fsize - $lastFetchedSize);
		if ($maxLength > 65536) $maxLength=(65536 / 2);
		$data=array();
		if ($maxLength > 0) {
			$fp=fopen("/temp/domoticz.log", 'r');
			fseek($fp, -$maxLength, SEEK_END);
			$data=explode("\n", fread($fp, $maxLength));

		}
		if (end($data) == "") array_pop($data);
		return json_encode(array("size" => $fsize, "file" => $this->log[$file], "data" => $data));
	}
	public function generateGUI() {
		?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Logs</title>
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="theme-color" content="#000">
<style type="text/css">
body{background-color:#000;color:#DDD;font-size:0.7em;}
.float{background:white;border-bottom:1px solid black;padding:10px 0 10px 0;margin:0px;height:30px;width:100%;text-align:left;}
.contents{margin-top:30px;}
.results{padding-bottom:20px;font-family:monospace;white-space:pre;}
</style>
<script src="/scripts/jquery.2.0.0.min.js"></script>
<script src="/scripts/jquery-ui.min.js"></script>
<script type="text/javascript">
	/* <![CDATA[ */
	lastSize=0
	documentHeight=0
	scrollPosition=0
	scroll=true
	lastFile=window.location.hash != "" ? window.location.hash.substr(1) : ""
	$(document).ready(function(){
		setInterval("updateLog()",2500)
		$(window).scroll(function(e){
			if ($(window).scrollTop() > 0) $('.float').css({position:'fixed',top:'0',left :'auto'})
			else $('.float').css({position:'static'})
		})
		$(window).scroll(function(){
			documentHeight=$(document).height();
			scrollPosition=$(window).height() + $(window).scrollTop();
			if (documentHeight <= scrollPosition) scroll=true;
			else scroll=false
		})
		scrollToBottom()
	})
	function scrollToBottom(){
		$("html, body").animate({scrollTop:$(document).height()},"fast")
	}
	function updateLog(){
		$.getJSON('?ajax=1&file=' + lastFile + '&lastsize=' + lastSize, function(data){
			lastSize=data.size
			$("#current").text(data.file)
			$.each(data.data, function(key, value){
				$("#results").append('' + value + '<br/>')
			})
			if (scroll) scrollToBottom()
		})
	}
	/* ]]> */
	function navigator_Go(url){window.location.assign(url)}

</script>
</head>
<body>
	<div class="fix z1" style="position:fixed;top:0px;left:0px;">
		<a href="javascript:navigator_Go('floorplan.php');">
			<img src="/images/close.png" width="48px" height="48px"/>
		</a>
	</div>
	<div class="contents">
		<div id="results" class="results"></div>
	</div>
<script src="/scripts/bootstrap.min.js"></script>
</body>
</html>
	<?php }
} ?>
