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
require 'secure/functions.php';
require 'secure/authentication.php';
$tail=new PHPTail(
    array(
    "Domoticz"=>"/temp/domoticz.log",
    "php8.0-fpm"=>"/var/log/php8.0-fpm.log",
    "nginxaccess"=>"/var/log/nginx/access.log",
    "nginxerror"=>"/var/log/nginx/error.log",
    "Fail2Ban"=>"/var/log/fail2ban.log",
    )
);
if (isset($_GET['ajax'])) {
    echo $tail->getNewLines($_GET['file'], $_GET['lastsize'], $_GET['grep'], $_GET['invert']);die();
}
$tail->generateGUI();
class PHPTail
{
    private $log="";
    private $updateTime;
    private $maxSizeToLoad;
    public function __construct($log,$defaultUpdateTime=2000,$maxSizeToLoad=2097152)
    {
        $this->log=is_array($log)?$log:array($log);
        $this->updateTime=$defaultUpdateTime;
        $this->maxSizeToLoad=$maxSizeToLoad;
    }
    public function getNewLines($file,$lastFetchedSize,$grepKeyword,$invert)
    {
        clearstatcache();
        if (empty($file)) {
            $file=key(array_slice($this->log, 0, 1, true));
        }
        $fsize=filesize($this->log[$file]);
        $maxLength=($fsize - $lastFetchedSize);
        if ($maxLength > $this->maxSizeToLoad) {
            $maxLength=($this->maxSizeToLoad / 2);
        }
        $data=array();
        if ($maxLength > 0) {

            $fp=fopen($this->log[$file], 'r');
            fseek($fp, -$maxLength, SEEK_END);
            $data=explode("\n", fread($fp, $maxLength));

        }
        if ($invert == 0) {
            $data=preg_grep("/$grepKeyword/", $data);
        } else {
            $data=preg_grep("/$grepKeyword/", $data, PREG_GREP_INVERT);
        }
        if (end($data) == "") {
            array_pop($data);
        }
        return json_encode(array("size" => $fsize, "file" => $this->log[$file], "data" => $data));
    }
    public function generateGUI()
    {
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Logs</title>

<!--<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />-->

<style type="text/css">
#grepKeyword, #settings {font-size: 80%;}
.float{background: white;border-bottom: 1px solid black;padding: 10px 0 10px 0;margin: 0px;height: 30px;width: 100%;text-align: left;}
.contents {margin-top: 30px;}
.results {padding-bottom: 20px;font-family: monospace;font-size: small;white-space: pre;}
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
<script type="text/javascript">
    /* <![CDATA[ */
    lastSize=0;
    grep="";
    invert=0;
    documentHeight=0;
    scrollPosition=0;
    scroll=true;
    lastFile=window.location.hash != "" ? window.location.hash.substr(1) : "";
    console.log(lastFile);
    $(document).ready(function(){
        $("#settings").dialog({
            modal : true,
            resizable : false,
            draggable : false,
            autoOpen : false,
            width : 590,
            height : 270,
            buttons : {
                Close : function(){
                    $(this).dialog("close");
                }
            },
            open : function(event, ui){
                scrollToBottom();
            },
            close : function(event, ui){
                grep=$("#grep").val();
                invert=$('#invert input:radio:checked').val();
                $("#results").text("");
                lastSize=0;
                $("#grepspan").html("Grep keyword: \"" + grep + "\"");
                $("#invertspan").html("Inverted: " + (invert == 1 ? 'true' : 'false'));
            }
        });
        $('#grep').keyup(function(e){
            if (e.keyCode == 13){
                $("#settings").dialog('close');
            }
        });
        $("#grep").focus();
        $("#grepKeyword").click(function(){
            $("#settings").dialog('open');
            $("#grepKeyword").removeClass('ui-state-focus');
        });
        $(".file").click(function(e){
            $("#results").text("");
            lastSize=0;
console.log(e);
            lastFile=$(e.target).text();
        });
        setInterval("updateLog()", <?php echo $this->updateTime; ?>);
        $(window).scroll(function(e){
            if ($(window).scrollTop() > 0){
                $('.float').css({
                    position : 'fixed',
                    top : '0',
                    left : 'auto'
                });
            } else {
                $('.float').css({
                    position : 'static'
                });
            }
        });
        $(window).resize(function(){
            if (scroll){
                scrollToBottom();
            }
        });
        //Handle if the window should be scrolled down or not
        $(window).scroll(function(){
            documentHeight=$(document).height();
            scrollPosition=$(window).height() + $(window).scrollTop();
            if (documentHeight <= scrollPosition){
                scroll=true;
            } else {
                scroll=false;
            }
        });
        scrollToBottom();

    });
    function scrollToBottom(){
        $("html, body").animate({scrollTop: $(document).height()}, "fast");
    }
    function updateLog(){
        $.getJSON('?ajax=1&file=' + lastFile + '&lastsize=' + lastSize + '&grep=' + grep + '&invert=' + invert, function(data){
            lastSize=data.size;
            $("#current").text(data.file);
            $.each(data.data, function(key, value){
                $("#results").append('' + value + '<br/>');
            });
            if (scroll){
                scrollToBottom();
            }
        });
    }
    /* ]]> */
    function navigator_Go(url){window.location.assign(url);}

</script>
</head>
<body>
    <div class="fix z1" style="position:sticky;top:5px;left:5px;">
        <a href="javascript:navigator_Go('logs.php');">
            <img src="/images/close.png" width="72px" height="72px"/>
        </a>
    </div>
    <div class="contents">
        <div id="results" class="results"></div>
        <div id="settings" title="PHPTail settings">
            <p>Grep keyword (return results that contain this keyword)</p>
            <input id="grep" type="text" value="" />
            <p>Should the grep keyword be inverted? (Return results that do NOT contain the keyword)</p>
            <div id="invert">
                <input type="radio" value="1" id="invert1" name="invert" /><label for="invert1">Yes</label>
                <input type="radio" value="0" id="invert2" name="invert" checked="checked" /><label for="invert2">No</label>
            </div>
        </div>
    </div>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>
    <?php }
} ?>