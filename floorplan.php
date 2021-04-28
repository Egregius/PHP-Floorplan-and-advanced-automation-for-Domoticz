<?php
require 'secure/functions.php';
$_SESSION['redirect']='https://home.egregius.be/floorplan.php';
require '/var/www/authentication.php';
createheader('floorplan');
?>
	<body class="floorplan">
		<div class="fix" id="clock">
			<a href='javascript:location.reload(true);' id="time">
				Loading...
			</a>
		</div>
		<div id="placeholder">
			<div class="fix z" style="top:0px;left:-285px;width:480px;height:820px;backgound-color:#F00;" onclick="javascript:location.reload(true)">
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				Push to refresh...<br>
				<br>
				<a href="/index.php">Menu</a>
			</div>
		</div>
	</body>
</html>
