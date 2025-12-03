<?php
require 'secure/functions.php';
$_SESSION['redirect']='https://home.egregius.be/floorplan.php';
require '/var/www/authentication.php';
createheader('floorplan');
?>
	<body class="floorplan">
		<div class="fix" id="clock">
			<a href="#" id="time" onclick="
				sessionStorage.removeItem('floorplanData');
				location.reload();
				return false;
			">
				Loading...
			</a>
		</div>
		<div id="placeholder">
		</div>
	</body>
</html>
