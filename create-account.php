<?php
include('./classes/dbh.php');
error_reporting(E_ALL);
ini_set("display_errors", "on");

if (isset($_POST['createaccount'])){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];
	$crypto_strong = True;
	$token = sha1(bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong)));

	//check if username already exist
	if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))){
		//check length of username
		if (strlen($username) >= 3 && strlen($username) <= 32) {
			if (strlen($password) >= 6 && strlen($password) <= 60){
				//check if username contains only excepted characters
				if (preg_match('/[a-zA-Z0-9_]+/', $username)) {
					//check if email is valid
					if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
						//check if email exist in db
						if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {
							//insert user in users table
							DB::query('INSERT INTO users VALUES(NULL, :username, :password, :email, \'0\', NULL, 0)', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));
							$userid = DB::query('SELECT * FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
							//inserting token in email validation table
							DB::query('INSERT INTO email_validation_token VALUES(NULL, :token, :userid)', array(':token'=>$token, ':userid'=>$userid));
							$to = $email;
							$subject = "Email Verification";
							$message = "<a href='http://127.0.0.1:8080/Camagru/login.php?token=$token'>Validate Email</a>";
							$headers = "From: camagru <admin@camagru.com>\r\n";
							$headers .= "Reply-To: camagru <admin@camagru.com>\r\n";
							$headers .= "Content-type: text/html\r\n";
							if (mail($to, $subject, $message, $headers)) {
								echo "Account successfully created - Please check your email for a message from us";
							}
						} else {
							echo "email already in use";
						}
					} else {
						echo "Invalid email";
					}
				} else {
					echo "Invalid username: only aA-zZ, 0-9, and '_' my be used";
				}
			} else {
				echo "Invalid password length: password must be between 6 - 60 chars long";
			}
		} else {
			echo "Invalid username: username must be between 3 - 32 chars long";
		}
	} else {
		echo "User already exists!";
	}
}
?>

<h1>Register</h1>
<form action="create-account.php" method="post">
	<input type="text" name="username" value=""	placeholder="Username ..."><p></p>
	<input type="password" name="password" value="" placeholder="Password ..."><p></p>
	<input type="email" name="email" value="" placeholder="someone@company.com"><p></p>
	<input type="submit" name="createaccount" value="Create Account">
</form>