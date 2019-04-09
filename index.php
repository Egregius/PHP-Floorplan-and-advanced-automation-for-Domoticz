<?php
/**
 * Pass2PHP
 * php version 7.0.33
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/settings.php';
if ($authenticated) {
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width,height=device-height, user-scalable=yes, minimal-ui" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link href="/styles/index.php" rel="stylesheet" type="text/css"/>
<style>
hr{display:block;height:1px;border:0;border-top:1px solid #777;margin:5px;padding:0;}
</style>
<title>Startpagina</title>
<script type="text/javascript">
function fullScreen(theURL) {
params  = \'width=\'+screen.width;
params += \', height=\'+screen.width;
params += \', location=1,status=1,scrollbars=1,menubar=1\';
testwindow= window.open (theURL, \'\', params);
testwindow.moveTo(0,0);
}
</script>
</head>
<body>
<div class="grid">
    <div class="grid-sizer"></div>
    <div class="gutter-sizer"></div>
    <div class="grid-item">';

    if ($home===true) {
        echo '<a href="https://home.egregius.be/floorplan.php" target="popup" class="btn b3" onclick="window.open(this.href,\'Floorplan\',\'left=0,top=0,width=509,height=870,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes\').focus(); return false;">Plan</a>
<a href="https://home.egregius.be/floorplan.heating.php" target="popup" class="btn b3" onclick="window.open(this.href,\'Floorplan\',\'left=0,top=0,width=509,height=870,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes\').focus(); return false;">Heating</a>
<a href="https://home.egregius.be/floorplan.media.php" target="popup" class="btn b3" onclick="window.open(this.href,\'Floorplan\',\'left=0,top=0,width=509,height=870,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes\').focus(); return false;">Media</a>
<a href="https://my.smappee.com/#home" target="popup" class="btn b3" onclick="window.open(this.href,\'Smappee\',\'left=0,top=0,width=509,height=870,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes\').focus(); return false;">Smappee</a>
<a href="https://home.egregius.be/tempbig.php" target="popup" class="btn b3" onclick="window.open(this.href,\'Temperaturen\',\'left=-10,top=-10,width=1920,height=1080,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no\').focus(); return false;">Temps</a>
<a href="https://home.egregius.be/bat.php" target="popup" class="btn b3">Bats</a>
<a href="http://home.egregius.be:8080/#/Dashboard" target="_blank" rel="noopener" class="btn b3">Dticz</a>
<a href="https://home.egregius.be/denon.php" target="popup" class="btn b3" onclick="window.open(this.href,\'Floorplan\',\'left=0,top=0,width=509,height=848,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes\').focus(); return false;">Denon</a>
<a href="https://home.egregius.be/logs.php" target="_blank" rel="noopener" class="btn b3">Logs</a>
<a href="https://verbruik.egregius.be" target="_blank" class="btn b3">Verbruik</a>
';
    }
    if ($home===true||$friend===true) {
        echo '<a href="https://films.egregius.be/films.php" target="_blank" rel="noopener" class="btn b3">Films</a>';
        if ($user=='Guy') {
            echo '<a href="https://redmine.egregius.be/issues?c%5B%5D=project&c%5B%5D=tracker&c%5B%5D=status&c%5B%5D=priority&c%5B%5D=subject&c%5B%5D=updated_on&f%5B%5D=status_id&f%5B%5D=project_id&f%5B%5D=&group_by=&op%5Bproject_id%5D=%21&op%5Bstatus_id%5D=o&set_filter=1&sort=updated_on%3Adesc%2Cid%3Adesc&t%5B%5D=spent_hours&t%5B%5D=&utf8=%E2%9C%93&v%5Bproject_id%5D%5B%5D=80" target="_blank" rel="noopener" class="btn b3">Redmine</a>';
        } else {
            echo '<a href="https://redmine.egregius.be/issues" target="_blank" rel="noopener" class="btn b3">Redmine</a>';
        }
    }
    if ($home===true) {
        echo '
<a href="https://finance.egregius.be" target="_blank" rel="noopener" class="btn b3">Finance</a>
<a href="https://sport.egregius.be" target="_blank" rel="noopener" class="btn b3">Sport</a>
<a href="https://zon.egregius.be" target="_blank" rel="noopener" class="btn b3">Zon</a>
<a href="https://egregius.be/sw/?page=index&search[tree]=cat0_z0_a4,cat0_z0_a6,cat0_z0_a7,cat0_z0_a8,cat0_z0_a9,&search[value][]=" target="_blank" rel="noopener" class="btn home b3">Spot</a>
<a href="http://diskstation.egregius.be:1601/" target="_blank" rel="noopener" class="btn home b3">Sab</a>';
    }
    if ($user=='Guy') {
        echo '<a href="http://diskstation.egregius.be:1599/" target="_blank" rel="noopener" class="btn home b3">Nas</a>
<a href="http://192.168.2.254:44300/" target="_blank" rel="noopener" class="btn home b3">pfSense</a>
<a href="http://192.168.2.254:44300/pfblockerng/pfblockerng_alerts.php" target="_blank" rel="noopener" class="btn home b3">pfBlocker</a>
<a href="http://192.168.2.254:44300/status_graph.php?if=lan&sort=in&filter=local&hostipformat=hostname&backgroundupdate=true" target="_blank" rel="noopener" class="btn home b3">traffic graph</a>
<a href="https://192.168.2.1:8006/#v1:0:=node%2Fproxmox:4:5::::20:35" target="_blank" rel="noopener" class="btn home b3">ProxMox</a>
<a href="http://192.168.2.2:10000/?dashboard" target="_blank" rel="noopener" class="btn home b3">Webmin</a>
<a href="https://home.egregius.be/secure/opcache.php" target="_blank" rel="noopener" class="btn home b3">opCache</a>
<a href="https://home.egregius.be/secure/adminer.php?sqlite=&username=&db=%2Fdomoticz%2Fdomoticz.db" target="_blank" rel="noopener" class="btn home b2">DomoSQL</a>
<a href="https://home.egregius.be/secure/phpMyAdmin/tbl_sql.php?db=domotica&table=devices&sql_query=SELECT+date_format%28from_unixtime%28t%29%2C%27%25d-%25m+%25H%3A%25i%3A%25s%27%29+AS+%60time%60+%2Cn%2C%28CASE+WHEN+s+%3E+1552460411+then+date_format%28from_unixtime%28s%29%2C%27%25d-%25m+%25H%3A%25i%3A%25s%27%29+ELSE+s+END%29+as+s%2C%28CASE+WHEN+m+%3E+1552460411+then+date_format%28from_unixtime%28m%29%2C%27%25d-%25m+%25H%3A%25i%3A%25s%27%29+ELSE+m+END%29+as+m%2Ci+FROM+%60devices%60+ORDER+BY+%60t%60+DESC+LIMIT+0%2C300&show_query=1#querybox" target="_blank" rel="noopener" class="btn home b2">MyAdmin</a>
';
    }
    if ($home===true) {
        echo '<hr><a href="https://home.egregius.be/picam1" target="_blank" rel="noopener" class="btn b2" onclick="window.open(this.href,\'PiCam1 Voordeur-Oprit\',\'left=0,top=0,width=1900,height=800,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes\').focus(); return false;">Voordeur-Oprit</a>
<a href="https://home.egregius.be/picam2" target="_blank" rel="noopener" class="btn b2" onclick="window.open(this.href,\'PiCam2 Alex\',\'left=0,top=0,width=1400,height=1080,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes\').focus(); return false;">Alex</a>
<a href="http://picam1.egregius.be/" target="_blank" rel="noopener" class="btn home b3">PiCam1</a>
<a href="http://picam2.egregius.be/" target="_blank" rel="noopener" class="btn home b3">PiCam2</a>
<a href="http://picam3.egregius.be/" target="_blank" rel="noopener" class="btn home b3">PiCam3</a>
';
    }
    echo '<hr>';
    if ($home===true||$friend===true) {
        echo '<a href="https://urbexforum.org/index.php?action=unread;all;start=0" target="_blank" rel="noopener" class="btn b3">Unread</a>';
    }
    if ($user=='Guy') {
        echo '
		<a href="http://secure.egregius.be/urbexforum/searchthemap.php" target="_blank" rel="noopener" class="btn home b3">Search</a>
        <a href="http://secure.egregius.be/urbexforum/BonusCredits.php" target="_blank" rel="noopener" class="btn home b3">BonusCr</a>
        <a href="http://secure.egregius.be/urbexforum/countnames.php" target="_blank" rel="noopener" class="btn home b4">Count rep</a>
        <a href="http://secure.egregius.be/urbexforum/updateactivity.php" target="_blank" rel="noopener" class="btn home b4">Activity</a>
        <a href="http://secure.egregius.be/urbexforum/vipapprovalalerts.php" target="_blank" rel="noopener" class="btn home b4">Alerts</a>
        <a href="http://secure.egregius.be/urbexforum/databasemaintenance.php" target="_blank" rel="noopener" class="btn home b4">clean</a>';
    }
    echo '<hr>';
    if ($home===true||$friend===true) {
        echo '
		<a href="https://egregius.be/" target="_blank" rel="noopener" class="btn b3">Egregius</a>
		<a href="https://minja.be" target="_blank" rel="noopener" class="btn b3">Minja</a>
		<a href="https://belgie.minja.be" target="_blank" rel="noopener" class="btn b3">Belgie</a>
        <a href="https://9lane.be" target="_blank" rel="noopener" class="btn b3">9lane</a>
        <a href="https://y013.be" target="_blank" rel="noopener" class="btn b3">y013</a>
        <a href="https://egregius.be/matomo/index.php?module=MultiSites&action=index&idSite=1&period=range&date=last30" target="_blank" rel="noopener" class="btn b3">Matomo</a>';
    }
    if ($user=='Guy') {
        echo '<hr>
        <a href="https://www.transip.eu/cp/domain-hosting/domain/prm/100034583/egregius.be/" target="_blank" rel="noopener" class="btn b3">transip</a>
        <a href="https://secure.egregius.be/opcache.php" target="_blank" rel="noopener" class="btn home b3">opCache</a>
        <a href="https://secure.egregius.be/apcu.php" target="_blank" rel="noopener" class="btn home b3">APCu</a>
        <a href="https://secure.egregius.be/phpmyadmin" target="_blank" rel="noopener" class="btn home b3">MyAdmin</a>
        <a href="http://95.170.95.33:1597/" target="_blank" rel="noopener" class="btn home b3">Webmin</a>
        <a href="https://secure.egregius.be/logs.php" target="_blank" rel="noopener" class="btn home b3">Logs</a>
		<a href="http://diskstation.egregius.be:28888/gui/" target="_blank" rel="noopener" class="btn home b2">ResilioSync</a>
		<a href="http://95.170.95.33:8888/gui/" target="_blank" rel="noopener" class="btn home b2">ResilioSync VPS</a>
	</div>
    <div class="grid-item">';
    }
    if ($user=='Guy') {
        echo '<a href="https://terminals.mynetpay.be/menu.php" target="_blank" rel="noopener" class="btn b1">Xafax</a>';
    }
    echo '
        <a href="http://www.verkeerscentrum.be/verkeersinfo/kaart?erp=3dQ6yOtK%2BduVafS2AWRUvVZUBCC44Oj17YdgIQzl8sl9J5ewPUbXRdhisbSWGvVsJy36R0Enl%2BlesHSC1L2rzA%3D%3D" target="_blank" rel="noopener" class="btn b1">Verkeerscentrum</a>
        <a href="http://www.touringmobilis.be/" target="_blank" rel="noopener" class="btn b2">Touring Mobilis</a>
        <a href="https://mydrive.tomtom.com/en_gb/#+viewport=50.8909,4.27642,9+ver=2" target="_blank" rel="noopener" class="btn b2">TomTom</a>
        <hr>
        <a href="https://www.windfinder.com/forecast/roeselare" target="_blank" rel="noopener" class="btn b2">Windfinder</a>
        <a href="https://darksky.net/forecast/50.892965,3.112529/si24/nl" target="_blank" rel="noopener" class="btn b2">DarkSky</a>
        <a href="http://www.meteo.be/meteo/view/nl/211772-Verwachtingen.html" target="_blank" rel="noopener" class="btn b3">Meteo.be</a>
        <a href="http://meteox.be/h.aspx?r=&jaar=-3&soort=loop1uur" target="_blank" rel="noopener" class="btn b3">Meteox</a>
        <a href="http://weerslag.be/bliksem" target="_blank" rel="noopener" class="btn b3">Weerslag</a>
        <a href="https://www.buienradar.be/belgie/neerslag/buienradar/actueel" target="_blank" rel="noopener" class="btn b3">Buienradar</a>
        <a href="https://www.buienradar.be/weer/beitem/be/2802384/14daagse" target="_blank" rel="noopener" class="btn b3">14daagse</a>
        <a href="https://www.google.be/search?q=weerbericht+beitem" target="_blank" rel="noopener" class="btn b3">Google</a>
        <hr>
        <a href="https://www.vrt.be/vrtnws/nl/net-binnen/" target="_blank" rel="noopener" class="btn b2">VRT NWS</a>
        <a href="https://news.google.com/news/?ned=nl_be&hl=nl" target="_blank" rel="noopener" class="btn b2">Google News</a>
        <a href="https://www.yeloplay.be/bewaard/opgenomen" target="_blank" rel="noopener" class="btn b3">Yelo.TV</a>
        ';
    if ($user=='Guy') {
        echo '
		<a href="https://www.telenet.be/mijntelenet/rgw/settings.do?action=showAdvancedSettings&identifier=w307192" target="_blank" rel="noopener" class="btn b3">Telenet</a>
        <a href="https://groenemeterstanden.eandis.be/installatie/nl/PVZ018438#meterstanden" target="_blank" rel="noopener" class="btn b3">Eandis</a>';
    }
    echo '<a href="http://app.photoephemeris.com/?ll=50.892965,3.112529&center=50.8930,3.1125&z=18&spn=0.00,0.01" target="_blank" rel="noopener" class="btn b2">Efemeriden</a>
        <a href="http://www.imdb.com/user/ur35504447/watchlist?start=1&view=detail&defaults=1&lists=watchlist&sort=date_added,desc&mode=detail&page=1" target="_blank" rel="noopener" class="btn b2">IMDB</a>
        <hr>
        <a href="https://www.iex.nl/Beleggingsfonds-Koers/60121373/AEGON-Equity-Fund.aspx" target="_blank" rel="noopener" class="btn b3">Aegon</a>
        <a href="http://character-code.com/" target="_blank" rel="noopener" class="btn b3">Char Codes</a>
        <a href="http://jodies.de/ipcalc?host=101.224.0.0&mask1=13&mask2=" target="_blank" rel="noopener" class="btn b3">IP Calc</a>
        <a href="http://noz.day-break.net/webcolor/" target="_blank" rel="noopener" class="btn b3">Colors</a>
        <a href="https://en.wikipedia.org/wiki/List_of_TCP_and_UDP_port_numbers" target="_blank" rel="noopener" class="btn b3">TCP Ports</a>
        <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms681381%28v=vs.85%29.aspx" target="_blank" rel="noopener" class="btn b3">MSDN Codes</a>
    </div>
    <div class="grid-item">
        <a href="https://www.google.be/" target="_blank" rel="noopener" class="btn b2">Google</a>
        <a href="https://mail.google.com/mail/u/0/?shva=1#inbox" target="_blank" rel="noopener" class="btn b2">GMail</a>
        <a href="https://www.google.com/calendar/" target="_blank" rel="noopener" class="btn b2">Calendar</a>
        <a href="https://drive.google.com/drive/my-drive" target="_blank" rel="noopener" class="btn b2">Drive</a>
        <a href="https://www.google.be/maps/@50.9020861,3.1064103,10.13z?hl=nl" target="_blank" rel="noopener" class="btn b2">Maps</a>
        <a href="https://www.google.com/maps/d/u/0/?hl=nl" target="_blank" rel="noopener" class="btn b2">My Maps</a>
        <a href="https://translate.google.be/?hl=nl&sa=N&q=" target="_blank" rel="noopener" class="btn b2">Translate</a>
        <a href="https://www.google.com/adsense/new/u/0/pub-1425264282291013/main/viewreports?rt=n&uim=d&d=alltime&ag=channel&dd=&ss=&oo=ascending&gm=earnings&co=o&drh=false&ct=td&oet=&ert=&se=&tz=a" target="_blank" rel="noopener" class="btn b2">Adsense</a>
        <a href="https://www.facebook.com/?sk=h_chr" target="_blank" rel="noopener" class="btn b2">Facebook</a>
        <a href="https://www.messenger.com" target="_blank" rel="noopener" class="btn b2">Messenger</a>
        <a href="https://ticktick.com/#p/58df653746a7110379f8cce0/tasks" target="_blank" rel="noopener" class="btn b1">TickTick</a>
        <a href="https://feedly.com/i/latest" target="_blank" rel="noopener" class="btn b1">Feedly</a>
        <hr>
        <a href="https://tweakers.net" target="_blank" rel="noopener" class="btn b2">Tweakers</a>
        <a href="https://github.com/Egregius" target="_blank" rel="noopener" class="btn b2">Github</a>
   </div>
    <div class="grid-item">
        <a href="http://www.apple.com/be-nl/shop" target="_blank" rel="noopener" class="btn b2">Apple Store</a>
        <a href="http://www.ibood.com/be/nl/all-deals/" target="_blank" rel="noopener" class="btn b2">Coolblue</a>
        <a href="http://www.ibood.com/be/nl/all-deals/" target="_blank" rel="noopener" class="btn b2">iBood</a>
        <a href="http://www.dagactie.nl/" target="_blank" rel="noopener" class="btn b2">Dagactie</a>
        <br/><a href="https://www.codima.be/" target="_blank" rel="noopener" class="btn b1">Codima</a>
        <br/><a href="https://www.betaalbare-domotica.be/" target="_blank" rel="noopener" class="btn b2">Bet Domo</a>
        <a href="http://www.robbshop.nl/" target="_blank" rel="noopener" class="btn b2">RobbShop</a>
        <br/><a href="http://www.cameranu.nl/" target="_blank" rel="noopener" class="btn b3">CameraNu</a>
        <a href="http://kameraexpress.nl/" target="_blank" rel="noopener" class="btn b3">KameraExpr</a>
        <a href="http://www.fotokonijneberg.be" target="_blank" rel="noopener" class="btn b3">Konijneberg</a>
        <br/><a href="http://my.benl.ebay.be/ws/eBayISAPI.dll?MyEbay&gbh=1" target="_blank" rel="noopener" class="btn b3">eBay</a>
        <a href="http://www.dhgate.com/myaccount/index.do" target="_blank" rel="noopener" class="btn b3">DH Gate</a>
        <a href="http://www.vandenborre.be/" target="_blank" rel="noopener" class="btn b3">Vandenborre</a>';
    if ($user=='Guy') {
        echo '
        <hr>
        <a href="https://www.keytradebank.be/" target="_blank" rel="noopener" class="btn b3">Keytrade</a>
        <a href="https://www.argenta.be/portalserver/argenta/home" target="_blank" rel="noopener" class="btn b3">Argenta</a>
        <a href="https://www.ing.be/nl/retail/login" target="_blank" rel="noopener" class="btn b3">ING</a>';
    } elseif ($user=='Kirby') {
        echo '
        <hr>
        <a href="https://www.keytradebank.be/" target="_blank" rel="noopener" class="btn b2">Keytrade</a>
        <a href="https://www.argenta.be/portalserver/argenta/home" target="_blank" rel="noopener" class="btn b2">Argenta</a>';
    }
    echo '
    </div>
    <div class="grid-item">
    	<a href="https://dashboard.health.nokia.com" target="_blank" rel="noopener" class="btn b2">Health Nokia</a>
		<a href="https://www.fitbit.com/" target="_blank" rel="noopener" class="btn b2">Fitbit</a>
		<a href="http://www.myfitnesspal.com/nl/" target="_blank" rel="noopener" class="btn b2">myFitnesspal</a>
		<a href="https://connect.garmin.com/modern/" target="_blank" rel="noopener" class="btn b2">Garmin Connect</a><br>
		<a href="https://log.concept2.com/log" target="_blank" rel="noopener" class="btn b2">Concept2</a>
		<a href="https://docs.google.com/spreadsheets/d/1p6ff8siUr4h_nOUFqgJEXmcmuDEN9YHtqOO7AKvpeyA/edit#gid=0" target="_blank" rel="noopener" class="btn b2">GDrive</a><br>
		<a href="https://log.concept2.com/rankings/2018/rower/100?gender=M" target="_blank" rel="noopener" class="btn b2">100m M</a>
		<a href="https://log.concept2.com/rankings/2018/rower/100?gender=F" target="_blank" rel="noopener" class="btn b2">100m V</a><br>
		<a href="https://log.concept2.com/rankings/2018/rower/500?gender=M" target="_blank" rel="noopener" class="btn b2">500m M</a>
		<a href="https://log.concept2.com/rankings/2018/rower/500?gender=F" target="_blank" rel="noopener" class="btn b2">500m V</a><br>
		<a href="https://log.concept2.com/rankings/2018/rower/2000?gender=M" target="_blank" rel="noopener" class="btn b2">2000m M</a>
		<a href="https://log.concept2.com/rankings/2018/rower/2000?gender=F" target="_blank" rel="noopener" class="btn b2">2000m V</a><br>
		<a href="https://log.concept2.com/rankings/2018/rower/5000?gender=M" target="_blank" rel="noopener" class="btn b2">5000m M</a>
		<a href="https://log.concept2.com/rankings/2018/rower/5000?gender=F" target="_blank" rel="noopener" class="btn b2">5000m V</a><br>
		<hr>
		<a href="https://kookboek.egregius.be/" target="_blank" rel="noopener" class="btn b1">kookboek</a>
		<a href="http://www.ilovehealth.nl/" target="_blank" rel="noopener" class="btn b2">ilovehealth</a>
		<a href="https://www.lekkervanbijons.be/recepten/type/hoofdgerecht/product/vlees" target="_blank" rel="noopener" class="btn b2">lekkervanbijons</a>
		<a href="http://www.vtiroeselare.be/weekmenu/" target="_blank" rel="noopener" class="btn b2">Weekmenu Tobi</a>
		<a href="https://www.schoolbeitem.be/sites/schoolbeitem/files/wysiwyg/menu_'.strtolower(strftime('%B_%Y', $_SERVER['REQUEST_TIME'])).'.pdf" target="_blank" rel="noopener" class="btn b2">Maandmenu Alex</a>
		<hr>
		<a href="https://www.booking.com" target="_blank" rel="noopener" class="btn b2">booking</a>
		<a href="https://www.casapilot.com/nl" target="_blank" rel="noopener" class="btn b2">Casapilot</a>
		<hr>
		<a href="https://egregius.be/matomo/index.php?module=MultiSites&action=index&idSite=1&period=range&date=last30" target="_blank" rel="noopener" class="btn b1">Matomo Stats</a>
	</div>';

    echo '</div>
</div>
<div class="clear"></div>';
    //echo $_SERVER['HTTP_USER_AGENT'].'<br/>'.$udevice;
    echo '<br/><br/>
<div class="logout">&nbsp;&nbsp;<form method="POST"><input type="hidden" name="username" value="'.$user.'"/><input type="submit" name="logout" value="Logout" class="btn" style="min-width:4em;padding:0px;margin:0px;width:50px;height:39px;"/></form><br/><br/></div>';
} ?>
</body>
<script type="text/javascript" language="javascript" src="scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript" language="javascript" src="scripts/masonry.pkgd.min.js"></script>
<script language="javascript">
$('.grid').masonry({
  itemSelector: '.grid-item',
  columnWidth: '.grid-sizer',
  gutter: '.gutter-sizer',
  percentPosition: true
});
</script>
</html>
