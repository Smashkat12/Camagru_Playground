<?php
include('./classes/dbh.php');
error_reporting(E_ALL);
ini_set("display_errors", "on");
if (isset($_POST['resetpassword'])) {
	$crypto_strong = True;
	$token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
	$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
	$user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
	DB::query('INSERT INTO password_tokens VALUES (NULL, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
	$to = DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))[0]['email'];
	if ($to) {
		$subject = " Camagru Password Reset";
		$message = "<a href='http://127.0.0.1:8080/Camagru/change-password.php?token=$token'>Reset Passwod</a>";
		$headers = "From: camagru <admin@camagru.com>\r\n";
		$headers .= "Reply-To: camagru <admin@camagru.com>\r\n";
		$headers .= "Content-type: text/html\r\n";
		if(mail($to, $subject, $message, $headers)) {
			echo "Email sent!";
			echo '<br />';
		}
	} else {
		echo "email entered does not match any on record";
	}
} 
?>


<h1>Forgot Password</h1>
<form action="forgot-password.php" method="post">
	<input type="text" name="email" value="" placeholder="Email ..."> <p></p>
	<input type="submit" name="resetpassword" value="Reset Password">
</form>