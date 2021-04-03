<html>
	<head>
		<style>
			/*.invisible{display:none;}*/
		</style>
	</head>
	<body>
		Nothing to see here.
		<div class="invisible">
			<h1>Should be invisible</h1>
			<form method="POST" action="dontdoit.php">
				<input type="text" name="subject" value="No!"/>
				<input type="submit" value="Do not click me!"/>
			</form>
		</div>
	</body>
</html>
