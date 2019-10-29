<?php
include('./classes/dbh.php');


if(isset($_POST['login'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	//check if username exist
	if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
		//verify password with hashed version stored in table
		if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])) {
			echo "Logged in";
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
			echo "Incorrect Password";
		}
	} else {
		echo "User not registered!";
	}
}



?>

<h1>Login to your account</h1>
<form action="login.php" method="post">
	<input type="text" name="username" value="" placeholder="Username ..."><p></p>
	<input type="password" name="password" value="" placeholder="Password ..."><p></p>
	<input type="submit" name="login" value="Login">
</form>