<?php
include('./classes/dbh.php');

if (isset($_GET['token'])) {
	$token = $_GET['token'];
	if (DB::query('SELECT token FROM email_validation_token WHERE token=:token', array(':token'=>$token))) {
		$userid = DB::query('SELECT user_id FROM email_validation_token WHERE token=:token', array(':token'=>$token))[0]['user_id'];
		DB::query('UPDATE users SET userActive=1 WHERE id=:userid', array(':userid' => $userid));
		DB::query('DELETE FROM email_validation_token WHERE token=:token', array(':token'=>$token));
		echo "User successfully activated";
	}
}
if(isset($_POST['login'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	//check if username exist
	if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
		//check if user account has been validated via email.
		if (DB::query('SELECT username FROM users WHERE username=:username AND userActive=1', array(':username'=>$username))) {
			//verify password with hashed version stored in table
			if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])) {

				//create token
				$crypto_strong = True;
				$token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
				//get user_id
				$user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
				//populate login_token table with sha1 hashed token & user_id
				DB::query('INSERT INTO login_tokens VALUES (NULL, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
				//set cookie to store login token of the user
				setcookie("CMGRUID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, true);
				setcookie("CMGRUID_2", 1, time() + 60 * 60 * 24 * 3, '/', NULL, NULL, true);
			} else {
				echo "<script>alert('Incorrect Password')</script>";
			}
		} else {
			echo "<script>alert('Please check your email and validate account in order to again access to Camagru profile')</script>";
		}
	} else {
		echo "<script>alert('User not registered!')</script>";
	}
}
?>