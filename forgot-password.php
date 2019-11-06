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
	$subject = " Camagru Password Reset";
	$message = "<a href='http://127.0.0.1:8080/Camagru/login.php?token=$token'>Validate Email</a>";
	$headers = "From: camagru <admin@camagru.com>\r\n";
	$headers .= "Reply-To: camagru <admin@camagru.com>\r\n";
	$headers .= "Content-type: text/html\r\n";
	if(mail($to, $subject, $message, $headers)) {
		echo "Email sent!";
		echo '<br />';
		echo $token;
	}
} else if (isset($_POST['changepassword'])) {
	$passwrd = $_POST['password'];
	$passwrd_re = $_POST['password_re'];
	
	if ($password != $password_re) {
		echo "password fields dont mathc";
		exit();
	} else {
		$user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
		DB::query('UPDATE users SET password=:passwrd WHERE id=:userid', array(':passwrd'=>password_hash($passwrd, PASSWORD_BCRYPT), ':userid' => $userid));
	}
}

?>
<?php
if (!isset($_GET['token'])) {
	echo '<h1>Forgot Password</h1>
	<form action="forgot-password.php" method="post">
		<input type="text" name="email" value="" placeholder="Email ..."> <p></p>
		<input type="submit" name="resetpassword" value="Reset Password">
	</form>';
} else {
	echo '<h1>Reset Password</h1>
	<form action="forgot-password.php?" method="post">
		<input type="password" name="password" value="" placeholder="Enter Password"> <p></p>
		<input type="password" name="password_re" value="" placeholder="Re-enter Password"> <p></p>
		<input type="submit" name="changepassword" value="Reset Password">
	</form>';
}
?>