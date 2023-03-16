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
		</div>
	</body>
</html>
