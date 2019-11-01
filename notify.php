<?php
include('./classes/dbh.php');
include('./classes/login.inc.php');

$showTimeline = false;
if (Login::isLoggedin()) {
	$userid = Login::isLoggedin();
} else {
	echo "Not Logged in";
}
echo "<h1>Notifications</h1>";
if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid'=>$userid))) {
	$notifications = DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid'=>$userid));

	foreach($notifications as $n) {
		if ($n['type'] == 1) {
			$senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
			echo $senderName." mentioned you in a post!<hr>";
		}
	}
}
?>