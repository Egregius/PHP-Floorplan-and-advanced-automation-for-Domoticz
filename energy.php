<?php
require 'secure/functions.php';
$_SESSION['redirect']='https://home.egregius.be/energy.php';
require '/var/www/authentication.php';
createheader('energy');
?>
	<body class="floorplan">
		<div id="placeholder">
		</div>
	</body>
</html>
