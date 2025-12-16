<?php
require 'secure/functions.php';
require '/var/www/authentication.php';
createheader('floorplan');
?>
	<body class="floorplan">
		<div class="abs" id="clock">
			<a href="#" id="time" onclick="
				clearTimeout(ajaxTimer);
				sessionStorage.clear();
				location.reload();
				initview();
				return false;
			">
				Loading...
			</a>
		</div>
		<div id="placeholder">
		</div>
	</body>
</html>
