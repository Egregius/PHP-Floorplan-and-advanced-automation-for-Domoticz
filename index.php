<?php require '/var/www/authentication.php'; ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<title>Floorplan</title>
		<meta name="viewport" content="width=device-width,initial-scale=<?= $scale ?>,user-scalable=yes,minimal-ui">
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="theme-color" content="#000">
		<link rel="manifest" href="/manifest.json">
		<link rel="shortcut icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-startup-image" href="images/domoticzphp144.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.css?v=2">
		<script src="/scripts/floorplanjs.js?v=2"></script>
		<script type="text/javascript">document.addEventListener('DOMContentLoaded',function(){setView('floorplan')});</script>
	</head>
	<body class="floorplan">
		<div class="abs" id="clock"><a href="#" id="time" onclick="location.reload();">Loading...</a></div>
		<div id="placeholder">
			<div id="floorplan" class="view active">
				<div class="abs leftbuttons" id="heatingbutton" onclick="setView('floorplanheating')"><img src="/images/arrowdown.png" class="i60" alt="Open"></div>
				<div class="fix floorplan2icon" onclick="setView('floorplanothers')"><img src="/images/plus.png" class="i60" alt="plus"></div>
				<div class="abs weg" id="weg"></div>
				<div class="abs z2" id="sirene"></div>
				<div class="abs yellow" id="rbureel"></div>
				<div class="abs yellow" id="rkeukenl"></div>
				<div class="abs yellow" id="rkeukenr"></div>
				<div class="abs yellow" id="rliving"></div>
				<div class="abs yellow" id="rkamerl"></div>
				<div class="abs yellow" id="rkamerr"></div>
				<div class="abs yellow" id="rwaskamer"></div>
				<div class="abs yellow" id="ralex"></div>
				<div class="abs z0" id="raamalex"></div>
				<div class="abs z0" id="raamwaskamer"></div>
				<div class="abs z0" id="raamliving"></div>
				<div class="abs z0" id="raamkeuken"></div>
				<div class="abs z0" id="raamkamer"></div>
				<div class="abs z0" id="raamhall"></div>
				<div class="abs z0" id="achterdeur"></div>
				<div class="abs z0" id="deurvoordeur"></div>
				<div class="abs z0" id="deurbadkamer"></div>
				<div class="abs z0" id="deurinkom"></div>
				<div class="abs z0" id="deurgarage"></div>
				<div class="abs z0" id="deurwc"></div>
				<div class="abs z0" id="deurkamer"></div>
				<div class="abs z0" id="deurwaskamer"></div>
				<div class="abs z0" id="deuralex"></div>
				<div class="abs z0" id="zlivinga"></div>
				<div class="abs z0" id="zlivingb"></div>
				<div class="abs z0" id="zkeuken"></div>
				<div class="abs z0" id="zinkom"></div>
				<div class="abs z0" id="zgarage"></div>
				<div class="abs z0" id="zhalla"></div>
				<div class="abs z0" id="zhallb"></div>
				<div class="abs z0" id="zalex"></div>
				<div class="abs z0" id="alwayson"></div>
				<div class="abs z0" id="daikin_kwh"></div>
				<div class="abs z1" id="dysonlader"></div>
				<div class="abs" id="living_temp" onclick="location.href='temp.php?living=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="living_temp_mercury" class="thermometer-mercury"></div>
					<div id="living_temp_avg" class="average-line"></div>
					<div id="living_temp_display" class="temp-display"></div>
					<div id="living_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="badkamer_temp" onclick="location.href='temp.php?badkamer=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="badkamer_temp_mercury" class="thermometer-mercury"></div>
					<div id="badkamer_temp_avg" class="average-line"></div>
					<div id="badkamer_temp_display" class="temp-display"></div>
					<div id="badkamer_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="kamer_temp" onclick="location.href='temp.php?kamer=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="kamer_temp_mercury" class="thermometer-mercury"></div>
					<div id="kamer_temp_avg" class="average-line"></div>
					<div id="kamer_temp_display" class="temp-display"></div>
					<div id="kamer_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="waskamer_temp" onclick="location.href='temp.php?waskamer=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="waskamer_temp_mercury" class="thermometer-mercury"></div>
					<div id="waskamer_temp_avg" class="average-line"></div>
					<div id="waskamer_temp_display" class="temp-display"></div>
					<div id="waskamer_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="alex_temp" onclick="location.href='temp.php?alex=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="alex_temp_mercury" class="thermometer-mercury"></div>
					<div id="alex_temp_avg" class="average-line"></div>
					<div id="alex_temp_display" class="temp-display"></div>
					<div id="alex_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="buiten_temp" onclick="location.href='temp.php?buiten=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="buiten_temp_mercury" class="thermometer-mercury"></div>
					<div id="buiten_temp_avg" class="average-line"></div>
					<div id="buiten_temp_display" class="temp-display"></div>
					<div id="buiten_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="zolder_temp" onclick="location.href='temp.php?zolder=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="zolder_temp_mercury" class="thermometer-mercury"></div>
					<div id="zolder_temp_avg" class="average-line"></div>
					<div id="zolder_temp_display" class="temp-display"></div>
					<div id="zolder_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs stamp" id="tpirliving"></div>
				<div class="abs stamp" id="tpirkeuken"></div>
				<div class="abs stamp" id="tpirgarage"></div>
				<div class="abs stamp" id="tpirinkom"></div>
				<div class="abs stamp" id="tpirhall"></div>
				<div class="abs stamp" id="traamliving"></div>
				<div class="abs stamp" id="traamkeuken"></div>
				<div class="abs stamp" id="traamkamer"></div>
				<div class="abs stamp" id="traamwaskamer"></div>
				<div class="abs stamp" id="traamalex"></div>
				<div class="abs stamp" id="tdeurvoordeur"></div>
				<div class="abs stamp" id="tdeurbadkamer"></div>
				<div class="abs stamp" id="tdeurinkom"></div>
				<div class="abs stamp" id="tdeurgarage"></div>
				<div class="abs stamp" id="tachterdeur"></div>
				<div class="abs stamp" id="tdeurkamer"></div>
				<div class="abs stamp" id="tdeurwaskamer"></div>
				<div class="abs stamp" id="tdeuralex"></div>
				<div class="abs stamp" id="tdeurwc"></div>
				<div class="abs stamp" id="talexslaapt"></div>
				<div class="abs" id="bose101"></div>
				<div class="abs" id="bose102"></div>
				<div class="abs" id="bose103"></div>
				<div class="abs" id="bose104"></div>
				<div class="abs" id="bose105"></div>
				<div class="abs" id="bose106"></div>
				<div class="abs" id="bose107"></div>
				<div class="abs z" id="alex" onclick="dimmer('alex')"></div>
				<div class="abs z" id="eettafel" onclick="dimmer('eettafel')"></div>
				<div class="abs z" id="bureellinks" onclick="dimmer('bureellinks')"></div>
				<div class="abs z" id="bureelrechts" onclick="dimmer('bureelrechts')"></div>
				<div class="abs z" id="kamer" onclick="dimmer('kamer')"></div>
				<div class="abs z" id="lichtbadkamer" onclick="dimmer('lichtbadkamer')"></div>
				<div class="abs z" id="terras" onclick="dimmer('terras')"></div>
				<div class="abs z" id="zithoek" onclick="dimmer('zithoek')"></div>
				<div class="abs z" id="inkom" onclick="dimmer('inkom')"></div>
				<div class="abs z" id="hall" onclick="dimmer('hall')"></div>
				<div class="abs z" id="wasbak" onclick="dimmer('wasbak')"></div>
				<div class="abs z" id="snijplank" onclick="dimmer('snijplank')"></div>
				<div class="abs z1 i48" id="lampkast"></div>
				<div class="abs z1 i48" id="tuin"></div>
				<div class="abs z1 i48" id="tuintafel"></div>
				<div class="abs z1 i48" id="kristal"></div>
				<div class="abs z1 i48" id="voordeur"></div>
				<div class="abs z1 i48" id="wc"></div>
				<div class="abs z1 i48" id="garage"></div>
				<div class="abs z1 i48" id="garageled"></div>
				<div class="abs z1 i48" id="zolderg"></div>
				<div class="abs z1 i48" id="steenterras"></div>
				<div class="abs z1 i48" id="poortrf"></div>
				<div class="abs z1 i48" id="daikin"></div>
				<div class="abs z1 i48" id="badkamervuur1"></div>
				<div class="abs z1 i48" id="badkamervuur2"></div>
				<div class="abs verbruik">
					<a href="https://verbruik.egregius.be/kwartierpiek.php">
						<div id="avg">
							<span id="avgtitle">15'</span>
							<span id="avgvalue"></span>
							<canvas id="avgtimecircle" width="120" height="120"></canvas>
							<canvas id="avgcircle" width="120" height="120"></canvas>
						</div>
					</a>
					<div id="net" onclick="location.href='https://hwenergy.app/dashboard?dashboard=6310D647-E7B5-4280-8D07-F829707D2D12'">
						<span id="nettitle">Net</span>
						<span id="netvalue"></span>
						<canvas id="netcircle" width="120" height="120"></canvas>
					</div>
					<div id="total" onclick="location.href='https://verbruik.egregius.be/dag.php?Guy=on#elec'">
						<span id="totaltitle">Verbruik</span>
						<span id="totalvalue"></span>
						<canvas id="totalcircle" width="120" height="120"></canvas>
					</div>
					<div id="elec" onclick="location.href='https://verbruik.egregius.be/dag.php?Guy=on#elec'">
						<span id="electitle">Elec</span>
						<span id="elecvalue"></span>
						<canvas id="eleccircle" width="120" height="120"></canvas>
					</div>
					<div id="gas" onclick="location.href='https://verbruik.egregius.be/dag.php?Guy=on#gas'">
						<span id="gastitle">Gas</span>
						<span id="gasvalue"></span>
						<canvas id="gascircle" width="120" height="120"></canvas>
					</div>
					<div id="bat" onclick="location.href='https://hwenergy.app/dashboard?dashboard=48AE6FA2-2D9E-486D-8AC5-9E14A2A1D391'">
						<span id="battitle">Bat</span>
						<span id="batvalue"></span>
						<canvas id="batcircle" width="120" height="120"></canvas>
						<canvas id="chargecircle" width="120" height="120"></canvas>
					</div>
				</div>
				<div class="abs zonurl" onclick="location.href='https://zon.egregius.be'">
					<div id="zon">
						<span id="zontitle">Zon</span>
						<span id="zonvalue"></span>
						<canvas id="zoncircle" width="120" height="120"></canvas>
					</div>
					<div id="zonv">
						<span id="zonvtitle">Zon</span>
						<span id="zonvvalue"></span>
						<canvas id="zonvcircle" width="120" height="120"></canvas>
					</div>
				</div>
				<div id="playlist" class="abs"></div>
			</div>
			<div id="floorplanothers" class="view">
				<div class="abs floorplan2icon" onclick="setView('floorplan')"><img src="/images/close.png" class="i60" alt="close"></div>
				<div class="fix z2" id="osirene"></div>
				<div class="abs z1 i48" id="ogrohered"></div>
				<div class="abs z1 i48" id="okookplaat"></div>
				<div class="abs z1 i48" id="onas"></div>
				<div class="abs z1 i48" id="omedia"></div>
				<div class="abs z1 i48" id="ozetel"></div>
				<div class="abs z1 i48" id="oboseliving"></div>
				<div class="abs z1 i48" id="obosekeuken"></div>
				<div class="abs yellow" id="orbureel"></div>
				<div class="abs yellow" id="orkeukenl"></div>
				<div class="abs yellow" id="orkeukenr"></div>
				<div class="abs yellow" id="orliving"></div>
				<div class="abs z0" id="ozlivinga"></div>
				<div class="abs z0" id="ozlivingb"></div>
				<div class="abs z0" id="ozkeuken"></div>
				<div class="abs z0" id="ozinkom"></div>
				<div class="abs" id="oraamliving"></div>
				<div class="abs" id="oraamkeuken"></div>
				<div class="abs" id="odeurvoordeur"></div>
				<div class="abs" id="odeurinkom"></div>
				<div class="abs" id="odeurgarage"></div>
				<div class="abs" id="odeurwc"></div>
				<div class="abs" id="obuiten_temp" onclick="location.href='temp.php?buiten=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="obuiten_temp_mercury" class="thermometer-mercury"></div>
					<div id="obuiten_temp_avg" class="average-line"></div>
					<div id="obuiten_temp_display" class="temp-display"></div>
					<div id="obuiten_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs z1 i48" style="width:70px;" id="oauto"></div>
				<div class="abs z1 i48" style="width:70px;" id="oregenpomp"></div>
				<div class="abs z1 i48" style="width:70px;" id="omac"></div>
				<div class="abs z1 i48" style="width:70px;" id="oipaddock"></div>
				<div class="abs z1 i48" style="width:70px;" id="owater"></div>
				<div class="abs blackmedia">
					<div class="fix z1 center" style="top:880px;left:320px;"><a href="javascript:navigator_Go('log.php');"><img src="/images/log.png" width="40" height="40"><br>Log</a></div>
					<div class="fix z1 center" style="top:880px;left:400px;"><a href="javascript:navigator_Go('floorplan.cache.php?nicestatus');"><img src="/images/log.png" width="40" height="40"><br>Cache</a></div>
					<div class="fix z1 center" style="top:300px;left:70px;width:424px;">
						<button onclick="ajaxcontrol('runsync','runsync','googlemaps');setView('floorplan');" class="btn b3">Google myMaps</button>
						<button onclick="ajaxcontrol('runsync','runsync','garmingpx');setView('floorplan');" class="btn b3">Garmin GPX</button>
						<button onclick="ajaxcontrol('runsync','runsync','garminbadges');setView('floorplan');" class="btn b3">Garmin Badges</button>
						<button class="btn b2" id="overlof" onclick="verlof();">Verlof</button>
						<button onclick="ajaxcontrol('runsync','runsync','weegschaal');setView('floorplan');" class="btn b2">Weegschaal</button>
					</div>
					<div class="abs" id="log"></div>
				</div>
				<div class="fix" id="omediasidebar">
					<a href="javascript:navigator_Go('https://films.egregius.be/films.php');"><img src="/images/kodi.png" class="i48"><br>Films</a><br><br>
					<a href="javascript:navigator_Go('https://films.egregius.be/series.php');"><img src="/images/kodi.png" class="i48"><br>Series</a><br><br>
					<a href="javascript:navigator_Go('kodicontrol.php');"><img src="/images/kodi.png" class="i48"><br>Kodi<br>Control</a><br><br>
					<a href="javascript:navigator_Go('kodi.php');"><img src="/images/kodi.png" class="i48"><br>Kodi</a><br><br>
					<div class="fix z1 splitbill"><a href="javascript:navigator_Go('https://finance.egregius.be/splitbill/index.php');"><img src="/images/euro.png" width="48" height="48" alt="Euro"></a>					</div>
				</div>
			</div>
			<div id="floorplanheating" class="view">
				<div class="abs floorplan2icon" onclick="setView('floorplan');"><img src="/images/close.png" class="i60" alt="plus"></div>
				<div class="abs leftbuttons" id="hheatingbutton"><img src="/images/arrowdown.png" class="i60" alt="Open"></div>
				<div class="fix z2" id="hsirene"></div>
				<div class="abs z1" style="top:343px;left:415px;"><a href="javascript:navigator_Go('floorplan.doorsensors.php');"><img src="/images/close.png" width="72" height="72" alt="Close"></a></div>
				<div class="abs z1 i48" id="hdaikin"></div>
				<div class="abs z1 i48" id="hbadkamervuur1"></div>
				<div class="abs z1 i48" id="hbadkamervuur2"></div>
				<div class="abs z1 i48" id="hwasdroger"></div>
				<div class="abs yellow" id="hrbureel"></div>
				<div class="abs yellow" id="hrkeukenl"></div>
				<div class="abs yellow" id="hrkeukenr"></div>
				<div class="abs yellow" id="hrliving"></div>
				<div class="abs yellow" id="hrkamerl"></div>
				<div class="abs yellow" id="hrkamerr"></div>
				<div class="abs yellow" id="hrwaskamer"></div>
				<div class="abs yellow" id="hralex"></div>
				<div class="abs z0" id="hraamalex"></div>
				<div class="abs z0" id="hraamwaskamer"></div>
				<div class="abs z0" id="hraamliving"></div>
				<div class="abs z0" id="hraamkeuken"></div>
				<div class="abs z0" id="hraamkamer"></div>
				<div class="abs z0" id="hraamhall"></div>
				<div class="abs z0" id="hachterdeur"></div>
				<div class="abs z0" id="hdeurvoordeur"></div>
				<div class="abs z0" id="hdeurbadkamer"></div>
				<div class="abs z0" id="hdeurinkom"></div>
				<div class="abs z0" id="hdeurgarage"></div>
				<div class="abs z0" id="hdeurwc"></div>
				<div class="abs z0" id="hdeurkamer"></div>
				<div class="abs z0" id="hdeurwaskamer"></div>
				<div class="abs z0" id="hdeuralex"></div>
				<div class="abs z0" id="hzlivinga"></div>
				<div class="abs z0" id="hzlivingb"></div>
				<div class="abs z0" id="hzkeuken"></div>
				<div class="abs z0" id="hzinkom"></div>
				<div class="abs z0" id="hzgarage"></div>
				<div class="abs z0" id="hzhalla"></div>
				<div class="abs z0" id="hzhallb"></div>
				<div class="abs z0" id="halwayson"></div>
				<div class="abs z0" id="hdaikin_kwh"></div>
				<div class="abs" id="hliving_temp" onclick="location.href='temp.php?living=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="hliving_temp_mercury" class="thermometer-mercury"></div>
					<div id="hliving_temp_avg" class="average-line"></div>
					<div id="hliving_temp_display" class="temp-display"></div>
					<div id="hliving_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="hbadkamer_temp" onclick="location.href='temp.php?badkamer=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="hbadkamer_temp_mercury" class="thermometer-mercury"></div>
					<div id="hbadkamer_temp_avg" class="average-line"></div>
					<div id="hbadkamer_temp_display" class="temp-display"></div>
					<div id="hbadkamer_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="hkamer_temp" onclick="location.href='temp.php?kamer=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="hkamer_temp_mercury" class="thermometer-mercury"></div>
					<div id="hkamer_temp_avg" class="average-line"></div>
					<div id="hkamer_temp_display" class="temp-display"></div>
					<div id="hkamer_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="hwaskamer_temp" onclick="location.href='temp.php?waskamer=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="hwaskamer_temp_mercury" class="thermometer-mercury"></div>
					<div id="hwaskamer_temp_avg" class="average-line"></div>
					<div id="hwaskamer_temp_display" class="temp-display"></div>
					<div id="hwaskamer_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="halex_temp" onclick="location.href='temp.php?alex=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="halex_temp_mercury" class="thermometer-mercury"></div>
					<div id="halex_temp_avg" class="average-line"></div>
					<div id="halex_temp_display" class="temp-display"></div>
					<div id="halex_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="hbuiten_temp" onclick="location.href='temp.php?buiten=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="hbuiten_temp_mercury" class="thermometer-mercury"></div>
					<div id="hbuiten_temp_avg" class="average-line"></div>
					<div id="hbuiten_temp_display" class="temp-display"></div>
					<div id="hbuiten_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="hzolder_temp" onclick="location.href='temp.php?zolder=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="hzolder_temp_mercury" class="thermometer-mercury"></div>
					<div id="hzolder_temp_avg" class="average-line"></div>
					<div id="hzolder_temp_display" class="temp-display"></div>
					<div id="hzolder_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs" id="hgarage_temp" onclick="location.href='temp.php?garage=On'">
					<img src="/images/temp.png" class="thermometer-bg">
					<div id="hgarage_temp_mercury" class="thermometer-mercury"></div>
					<div id="hgarage_temp_avg" class="average-line"></div>
					<div id="hgarage_temp_display" class="temp-display"></div>
					<div id="hgarage_temp_trend" class="trend-arrow-container"></div>
				</div>
				<div class="abs z" id="hRrliving" onclick="roller('rliving');"></div>
				<div class="abs z" id="hRrbureel" onclick="roller('rbureel');"></div>
				<div class="abs z" id="hRrkeukenl" onclick="roller('rkeukenl');"></div>
				<div class="abs z" id="hRrkamerl" onclick="roller('rkamerl');"></div>
				<div class="abs z" id="hRrkamerr" onclick="roller('rkamerr');"></div>
				<div class="abs z" id="hRrwaskamer" onclick="roller('rwaskamer');"></div>
				<div class="abs z" id="hRralex" onclick="roller('ralex');"></div>
				<div class="abs stamp" id="htpirliving"></div>
				<div class="abs stamp" id="htpirkeuken"></div>
				<div class="abs stamp" id="htpirgarage"></div>
				<div class="abs stamp" id="htpirinkom"></div>
				<div class="abs stamp" id="htpirhall"></div>
				<div class="abs stamp" id="htraamliving"></div>
				<div class="abs stamp" id="htraamkeuken"></div>
				<div class="abs stamp" id="htraamkamer"></div>
				<div class="abs stamp" id="htraamwaskamer"></div>
				<div class="abs stamp" id="htraamalex"></div>
				<div class="abs stamp" id="htdeurvoordeur"></div>
				<div class="abs stamp" id="htdeurbadkamer"></div>
				<div class="abs stamp" id="htdeurinkom"></div>
				<div class="abs stamp" id="htdeurgarage"></div>
				<div class="abs stamp" id="htachterdeur"></div>
				<div class="abs stamp" id="htdeurkamer"></div>
				<div class="abs stamp" id="htdeurwaskamer"></div>
				<div class="abs stamp" id="htdeuralex"></div>
				<div class="abs stamp" id="htdeurwc"></div>
				<div class="abs z2 living_set" id="hliving_set" onclick="setpoint('living');"></div>
				<div class="abs z2 badkamer_set" id="hbadkamer_set" onclick="setpoint('badkamer');"></div>
				<div class="abs z2 kamer_set" id="hkamer_set" onclick="setpoint('kamer');"></div>
				<div class="abs z2 alex_set" id="halex_set" onclick="setpoint('alex');"></div>
				<div class="abs z" id="hluifel" onclick="roller('luifel');"></div>
				<div class="abs divsetpoints z">
					<table class="tablesetpoints">
						<tr>
							<td id="hbrander"></td>
							<td align="left" height="60" width="80" style="line-height:18px">
								Brander<br><span id="htbrander"></span>
							</td>
						</tr>
						<tr id="htrheating"></tr>
					</table>
				</div>
				<div class="abs z1 HUM"><a href="javascript:navigator_Go('/hum.php');">HUM</a></div>
			</div>
			<div id="floorplantemp" class="view"></div>
		</div>
		<div class="abs center zon">
			<img src="images/sunrise.png" alt="sunrise">
			<div id="dag"></div><br><small>&#x21e7;</small><span id="zonop"></span><br><small>&#x21e7;</small><span id="sunop"></span><br><small>&#x21e9;</small><span id="sunonder"></span><br><small>&#x21e9;</small><span id="zononder"></span><br>
			<div id="uv"></div>
			<a href=\'javascript:navigator_Go("https://www.buienradar.be/weer/Beitem/BE/2802384");\'><span id="buien"></span></a><br>
			<span id="wind"></span><br>
			<a href=\'javascript:navigator_Go("https://www.buienradar.be/weer/beitem/be/2802384/14daagse");\'><img src="" id="icon"></a>
			<span id="maxtemp"></span>
			<span id="mintemp"></span><br>
		</div>
</body>
</html>
