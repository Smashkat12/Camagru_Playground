<?php
include('./classes/dbh.php');
include('./classes/login.inc.php');

if (Login::isLoggedin()) {
	$userid = Login::isLoggedin();
} else {
	die("Not Logged in!");
}

if (isset($_POST['uploadprofileimg'])){
	//return encoded image as string
	$image = base64_encode(file_get_contents($_FILES['profileimg']['tmp_name']));
	//create stream
	$options = array('http'=>array(
		'method'=>"POST",
		'header'=>"Authorization: Bearer 8f071a0c68940eb80440a1315f56c5d0b5657ca2\n".
		"Content-Type: application/x-www-form-urlencoded",
		'content'=>$image
	));

	$context = stream_context_create($options);

	$imgurURL = "https://api.imgur.com/3/image";
	//check if size of image is less then 10mb
	if ($_FILES['profileimg']['size'] > 10240000) {
		die('Image size too big, must be 10mb or less!');
	}
	//open img file ussing HTTP headers set when creating stream
	$response = file_get_contents($imgurURL, false, $context);
	//return json object
	$response = json_decode($response);
	DB::query("UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':profileimg'=>$response->data->link, 'userid'=>$userid));
	
}
?>


<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
	Upload a profile image:
	<input type="file" name="profileimg">
	<input type="submit" name="uploadprofileimg" value="Upload Image">
</form>