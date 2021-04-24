<form method="POST">
	Hash password:
	<input type="text" name="password">
	<button type="submit">Hash</button>
</form>
<?php
if (isset($_POST['password'])) {
	$hash = password_hash($_POST['password'], PASSWORD_DEFAULT, ['cost' => 12]);
	echo $hash;
}
