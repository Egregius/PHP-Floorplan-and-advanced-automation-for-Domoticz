<form method="POST">
	Hash password:
	<input type="text" name="password">
	<button type="submit">Hash</button>
</form>
<?php
require 'functions.php';

if (isset($_POST['password'])) {
	$hash = password_hash($_POST['password'], PASSWORD_DEFAULT, ['cost' => 12]);
	echo $hash;
}

echo '<hr>';
if (isset($_POST['secret'])&&isset($_POST['2FAcode'])) {
	if ($tfa->verifyCode($_POST['secret'], $_POST['2FAcode']) === true) {
		$stmt=$db->prepare("UPDATE users SET 2fa = :2fa WHERE user=:user");
		$stmt->execute(array(':2fa'=>$_POST['secret'],':user'=>$_POST['user']));
		echo '2FA Enabled<br><br>';
	} else {
		echo 'Code invalid<br><br>';
	}
}


$secret = $tfa->createSecret(160);
echo $secret;
echo '<form method="POST">
				<input type="hidden" name="secret" value="'.$secret.'"/>
				Username:<input type="text" name="user">
				Code:<input type="number" name="2FAcode" value="" min="0" max="999999" style="width: 100px;text-align:center;"/>
				<br><br>
				<button type="submit" class="button success" >Save</button>
			</form>';
