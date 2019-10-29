<?php
include('./classes/dbh.php');

if (isset($_POST['resetpassword'])) {
	$crypto_strong = True;
	$token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
	$email = $_POST['email'];
	$user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
	DB::query('INSERT INTO password_tokens VALUES (NULL, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
	echo "Email sent!";
	echo '<br />';
	echo $token;
}

?>
<h1>Forgot Password</h1>
<form action="forgot-password.php" method="post">
	<input type="text" name="email" value="" placeholder="Email ..."> <p></p>
	<input type="submit" name="resetpassword" value="Reset Password">
</form>