<?php
function adminer_object() {
	include_once "./plugins/plugin.php";
	include_once "./plugins/login-password-less.php";
	return new AdminerPlugin(array(
		// TODO: inline the result of password_hash() so that the password is not visible in source codes
		new AdminerLoginPasswordLess(password_hash("4A7zKLXL3Mtf2CcKA2uHNrF9ow72WspMKKbAK3pBvXto4v2FZTpET3974WeGRpfqaEmNpeDfbD78M4BiUYziwEsND4dCBvSThgpT", PASSWORD_DEFAULT)),
	));
}
include "./adminer.php";
