<?php
include('./classes/dbh.php');
include('./classes/login.inc.php');
include('./classes/image.inc.php');

if (Login::isLoggedin()) {
	$userid = Login::isLoggedin();
} else {
	die("Not Logged in!");
}

if (isset($_POST['uploadprofileimg'])){
	Image::uploadImage('profileimg', "UPDATE users SET profileimg = :profileimg WHERE id=:userid", array('userid'=>$userid));
	
}
?>


<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
	Upload a profile image:
	<input type="file" name="profileimg">
	<input type="submit" name="uploadprofileimg" value="Upload Image">
</form>