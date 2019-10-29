<?php
include('./classes/dbh.php');
include('./classes/login.inc.php');

if (!Login::isLoggedin()) {
	die("Not Logged in.");
}

if (isset($_POST['confirm'])){
	if (isset($_POST['alldevices'])) {
		//delete all login token associated with user_id
		DB::query('DELETE FROM login_tokens WHERE user_id=:user_id', array(':user_id'=>Login::isLoggedin()));
	} else {
		if (isset($_COOKIE['CMGRUID'])) {
			//delete login token
			DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CMGRUID'])));
		}
		//expire the cookies
		setcookie('CMGRUID', 1, time() - 3600);
		setcookie('CMGRUID_2', 1, time() - 3600);

	}
}
?>

<h1>Logout of your Account</h1>
<p>Are you sure you'd like to logout?</p>
<form action="logout.php" method="post">
	<input type="checkbox" name="alldevices" value="alldevices"> Logout of all devices?<br />
	<input type="submit" name="confirm" value="Confirm">
</form>
