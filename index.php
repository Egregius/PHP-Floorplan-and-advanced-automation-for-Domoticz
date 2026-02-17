<?php require '/var/www/authentication.php'; ?>
<!DOCTYPE HTML>
<html lang="nl">
	<head>
		<link rel="dns-prefetch" href="//192.168.2.22">
		<link rel="preconnect" href="ws://192.168.2.22:9001">
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<title>Floorplan</title>
		<meta name="viewport" content="width=device-width,initial-scale=<?= $scale ?>,user-scalable=yes,minimal-ui">
		<meta name="viewport"
      		content="width=device-width,
               initial-scale=<?= $scale ?>,
               minimum-scale=<?= $scale ?>,
               maximum-scale=1.5,
               user-scalable=yes">

		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="theme-color" content="#000">
		<link rel="manifest" href="/manifest.json">
		<link rel="shortcut icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-startup-image" href="images/domoticzphp144.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.css?v=<?=filemtime('styles/floorplan.css.gz')?>">
		<script src="/scripts/mqtt.min.js?v=<?=filemtime('scripts/mqtt.min.js.gz')?>"></script>
		<script src="/scripts/floorplanjs.js?v=<?=filemtime('scripts/floorplanjs.js.gz')?>"></script>
		<script>document.addEventListener('DOMContentLoaded',function(){setView('floorplan')});</script>
	</head>
	<body class="floorplan">
		<div class="abs" id="clock" class="z2"><a href="#" id="time" onclick="location.reload();"><?= date("G:i:s");?></a></div>
		<div class="abs center zon">
			<div class="sun-times">
				☀️ <span id="dag"></span><br>
				<span id="Tstart"></span><br>
				<span id="Srise"></span><br>
				<span id="Sset"></span><br>
				<span id="Tend"></span><br>
				<span id="uv"></span>-<span id="uvm"></span><br>
				<div class="left windbuien">
				<span style="font-size:1.5em">💨</span> <span id="wind"></span><br>
				<span style="font-size:1.5em">☔️</span> <span id="buien"></span><br>
				</div>
				<img src="/images/03d.png" id="icon" alt="icon">
				<span id="maxtemp"></span>
				<span id="mintemp"></span>
				<span id="maxt"></span>
				<span id="mint"></span>
			</div>
			<div class="abs z2" id="sirene"></div>
			<div class="abs z1 i48" id="voordeur"></div>
			<div class="abs z1 i48" id="wc"></div>
			<div class="abs z1 i48" id="garage"></div>
			<div class="abs z1 i48" id="garageled"></div>
			<div class="abs z1 i48" id="zolderg"></div>
			<div class="abs z1 i48" id="poort"></div>
			<div class="abs z" id="inkom" onclick="dimmer('inkom')"></div>
			<div class="abs z" id="hall" onclick="dimmer('hall')"></div>
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
			<div class="abs z1 i48" id="daikin"></div>
			<div class="abs z1 i48" id="badkamervuur1"></div>
			<div class="abs z1 i48" id="badkamervuur2"></div>
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
			<div class="abs z1" id="buiten_temp" onclick="location.href='temp.php?buiten=On'">
				<img src="/images/temp.png" class="thermometer-bg" alt="buiten">
				<div id="buiten_temp_mercury" class="thermometer-mercury"></div>
				<div id="buiten_temp_avg" class="average-line"></div>
				<div id="buiten_temp_display" class="temp-display"></div>
				<div id="buiten_temp_trend" class="trend-arrow-container"></div>
			</div>
			<div class="abs z1" id="living_temp" onclick="location.href='temp.php?living=On'">
				<img src="/images/temp.png" class="thermometer-bg" alt="living">
				<div id="living_temp_mercury" class="thermometer-mercury"></div>
				<div id="living_temp_avg" class="average-line"></div>
				<div id="living_temp_display" class="temp-display"></div>
				<div id="living_temp_trend" class="trend-arrow-container"></div>
			</div>
			<div class="abs z1" id="badkamer_temp" onclick="location.href='temp.php?badkamer=On'">
				<img src="/images/temp.png" class="thermometer-bg" alt="badkamer">
				<div id="badkamer_temp_mercury" class="thermometer-mercury"></div>
				<div id="badkamer_temp_avg" class="average-line"></div>
				<div id="badkamer_temp_display" class="temp-display"></div>
				<div id="badkamer_temp_trend" class="trend-arrow-container"></div>
			</div>
			<div class="abs z1" id="kamer_temp" onclick="location.href='temp.php?kamer=On'">
				<img src="/images/temp.png" class="thermometer-bg" alt="kamer">
				<div id="kamer_temp_mercury" class="thermometer-mercury"></div>
				<div id="kamer_temp_avg" class="average-line"></div>
				<div id="kamer_temp_display" class="temp-display"></div>
				<div id="kamer_temp_trend" class="trend-arrow-container"></div>
			</div>
			<div class="abs z1" id="waskamer_temp" onclick="location.href='temp.php?waskamer=On'">
				<img src="/images/temp.png" class="thermometer-bg" alt="waskamer">
				<div id="waskamer_temp_mercury" class="thermometer-mercury"></div>
				<div id="waskamer_temp_avg" class="average-line"></div>
				<div id="waskamer_temp_display" class="temp-display"></div>
				<div id="waskamer_temp_trend" class="trend-arrow-container"></div>
			</div>
			<div class="abs z1" id="alex_temp" onclick="location.href='temp.php?alex=On'">
				<img src="/images/temp.png" class="thermometer-bg" alt="alex">
				<div id="alex_temp_mercury" class="thermometer-mercury"></div>
				<div id="alex_temp_avg" class="average-line"></div>
				<div id="alex_temp_display" class="temp-display"></div>
				<div id="alex_temp_trend" class="trend-arrow-container"></div>
			</div>
		</div>
		<div id="placeholder">
			<div id="floorplan" class="view active">
				<div class="abs leftbuttons" id="heatingbutton" onclick="setView('floorplanheating')"><img src="/images/arrowdown.png" class="i60" alt="Open"></div>
				<div class="fix floorplan2icon" onclick="setView('floorplanothers')"><img src="/images/plus.png" class="i60" alt="plus"></div>
				<div class="abs weg z" id="weg" onclick="weg();"><img src="/images/Thuis.png" id="wegimg"></div>
				<div class="abs" id="bose101"></div>
				<div class="abs" id="bose102"></div>
				<div class="abs" id="bose103"></div>
				<div class="abs" id="bose104"></div>
				<div class="abs" id="bose105"></div>
				<div class="abs" id="bose106"></div>
				<div class="abs" id="bose107"></div>
				<div class="abs z1 i48" id="lampkast"></div>
				<div class="abs z" id="alex" onclick="dimmer('alex')"></div>
				<div class="abs z" id="eettafel" onclick="dimmer('eettafel')"></div>
				<div class="abs z" id="bureellinks" onclick="dimmer('bureellinks')"></div>
				<div class="abs z" id="bureelrechts" onclick="dimmer('bureelrechts')"></div>
				<div class="abs z" id="kamer" onclick="dimmer('kamer')"></div>
				<div class="abs z" id="lichtbadkamer" onclick="dimmer('lichtbadkamer')"></div>
				<div class="abs z" id="zithoek" onclick="dimmer('zithoek')"></div>
				<div class="abs z" id="wasbak" onclick="dimmer('wasbak')"></div>
				<div class="abs z" id="snijplank" onclick="dimmer('snijplank')"></div>
				<div class="abs z" id="terras" onclick="dimmer('terras')"></div>
				<div class="abs z1 i48" id="tuin"></div>
				<div class="abs z1 i48" id="tuintafel"></div>
				<div class="abs z1 i48" id="steenterras"></div>
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
						<span id="batcharge"></span>
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
				<div id="info"></div>
			</div>

			<div id="floorplanothers" class="view">
				<div class="abs leftbuttons" id="oheatingbutton" onclick="setView('floorplanheating')"><img src="/images/arrowdown.png" class="i60" alt="Open"></div>
				<div class="abs floorplan2icon" onclick="setView('floorplan')"><img src="/images/close.png" class="i60" alt="close"></div>
				<div class="abs z1 i48" id="grohered"></div>
				<div class="abs z1 i48" id="kookplaat"></div>
				<div class="abs z1 i48" id="nas"></div>
				<div class="abs z1 i48" id="media"></div>
				<div class="abs z1 i48" id="zetel"></div>
				<div class="abs z1 i48" id="boseliving"></div>
				<div class="abs z1 i48" id="bosekeuken"></div>
				<div class="abs i48" style="width:70px;z-index:4;" id="auto"></div>
				<div class="abs z1 i48" style="width:70px;" id="mac"></div>
				<div class="abs z1 i48" style="width:70px;" id="ipaddock"></div>
				<div class="abs blackmedia">
					<div class="abs z1 center" style="top:590px;left:250px;"><a href="javascript:navigator_Go('log.php');"><img src="/images/log.png" width="40" height="40" alt="Log"><br>Log</a></div>
					<div class="abs z1 center" style="top:590px;left:320px;"><a href="javascript:navigator_Go('floorplan.cache.php?nicestatus');"><img src="/images/log.png" width="40" height="40" alt="Cache"><br>Cache</a></div>
					<div class="abs z1 center" style="top:0px;left:0px;width:100%;">
						<button onclick="ajaxcontrol('runsync','runsync','googlemaps');setView('floorplan');" class="btn b3">Google myMaps</button>
						<button onclick="ajaxcontrol('runsync','runsync','garmingpx');setView('floorplan');" class="btn b3">Garmin GPX</button>
						<button onclick="ajaxcontrol('runsync','runsync','garminbadges');setView('floorplan');" class="btn b3">Garmin Badges</button>
						<button class="btn b3" id="verlof" onclick="verlof();">Verlof</button>
						<button onclick="ajaxcontrol('runsync','runsync','weegschaal');setView('floorplan');" class="btn b3"">Weegschaal</button>
						<button onclick="ajaxcontrol('runsync','runsync','trakt');setView('floorplan');" class="btn b3">trakt.tv</button>
					</div>
					<div class="abs z2" id="log"></div>
				</div>
				<div class="fix" id="mediasidebar">
					<!--<a href="javascript:navigator_Go('https://films.egregius.be/films.php');"><img src="/images/kodi.png" class="i48" alt="Films"><br>Films</a><br><br>
					<a href="javascript:navigator_Go('https://films.egregius.be/series.php');"><img src="/images/kodi.png" class="i48" alt="Series"><br>Series</a><br><br>-->
					<br><br><br><br><br><br><br><br>
					<a href="javascript:navigator_Go('kodicontrol.php');"><img src="/images/kodi.png" class="i48" alt="Kodi Control"><br>Kodi<br>Control</a><br><br>
					<a href="javascript:navigator_Go('kodi.php');"><img src="/images/kodi.png" class="i48" alt="Kodi"><br>Kodi</a><br><br>
				</div>
			</div>

			<div id="floorplanheating" class="view">
				<div class="abs floorplan2icon" onclick="setView('floorplan');"><img src="/images/close.png" class="i60" alt="plus"></div>
				<div class="abs leftbuttons" id="heatingbutton"><img src="/images/arrowdown.png" class="i60" alt="Open"></div>
				<div class="abs z1" style="top:343px;left:415px;"><a href="javascript:navigator_Go('floorplan.doorsensors.php');"><img src="/images/close.png" width="72" height="72" alt="Close"></a></div>
				<div class="abs z1 i48" id="wasdroger"></div>
				<div class="abs z1 i48" style="width:70px;" id="water"></div>
				<div class="abs z1 i48" style="width:70px;" id="regenpomp"></div>
				<div class="abs z1" id="zolder_temp" onclick="location.href='temp.php?zolder=On'">
					<img src="/images/temp.png" class="thermometer-bg" alt="zolder">
					<div id="zolder_temp_mercury" class="thermometer-mercury"></div>
					<div id="zolder_temp_avg" class="average-line"></div>
					<div id="zolder_temp_display" class="temp-display"></div>
					<div id="zolder_temp_trend" class="trend-arrow-container"></div>
				</div>
				<!--
				<div class="abs z1" id="garage_temp" onclick="location.href='temp.php?garage=On'">
					<img src="/images/temp.png" class="thermometer-bg" alt="garage">
					<div id="garage_temp_mercury" class="thermometer-mercury"></div>
					<div id="garage_temp_avg" class="average-line"></div>
					<div id="garage_temp_display" class="temp-display"></div>
					<div id="garage_temp_trend" class="trend-arrow-container"></div>
				</div>
				-->
				<div class="abs z" id="Rrliving" onclick="roller('rliving');"></div>
				<div class="abs z" id="Rrbureel" onclick="roller('rbureel');"></div>
				<div class="abs z" id="Rrkeukenl" onclick="roller('rkeukenl');"></div>
				<div class="abs z" id="Rrkamerl" onclick="roller('rkamerl');"></div>
				<div class="abs z" id="Rrkamerr" onclick="roller('rkamerr');"></div>
				<div class="abs z" id="Rrwaskamer" onclick="roller('rwaskamer');"></div>
				<div class="abs z" id="Rralex" onclick="roller('ralex');"></div>

				<div class="abs z2 living_set" id="living_set" onclick="setpoint('living');"></div>
				<div class="abs z2 badkamer_set" id="badkamer_set" onclick="setpoint('badkamer');"></div>
				<div class="abs z2 kamer_set" id="kamer_set" onclick="setpoint('kamer');"></div>
				<div class="abs z2 alex_set" id="alex_set" onclick="setpoint('alex');"></div>
				<div class="abs z" id="luifel" onclick="roller('luifel');"></div>
				<div class="abs divsetpoints z">
					<table class="tablesetpoints">
						<tr><td id="brander"></td><td class="tbrander">Brander<br><span id="tbrander"></span></td></tr>
						<tr id="trheating"></tr>
					</table>
				</div>
				<div class="abs z1 HUM"><a href="javascript:navigator_Go('/hum.php');">HUM</a></div>
			</div>
			<div id="floorplantemp" class="view"></div>
		</div>
</body>
</html>
