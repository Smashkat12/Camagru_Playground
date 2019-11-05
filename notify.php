<?php
include('./classes/dbh.php');
include('./classes/login.inc.php');
include('./classes/notify.inc.php');

$showTimeline = false;
if (Login::isLoggedin()) {
	$userid = Login::isLoggedin();
} else {
	echo "Not Logged in";
}
echo "<h1>Notifications</h1>";
if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid'=>$userid))) {
	$notifications = DB::query('SELECT * FROM notifications WHERE receiver=:userid ORDER BY id DESC', array(':userid'=>$userid));

	foreach($notifications as $n) {
		//type 1 is post @mentions notifications
		if ($n['type'] == 1) {
			$senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
			$extra = json_decode($n['extra']);
			echo $senderName." mentioned you in a post! - ".$extra->postbody."<hr>";
		//type 2 is post likes notifications
		} else if ($n['type'] == 2) {
			$senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
			echo $senderName." liked your post!<hr>";
		//type 3 is post comments notifications
		} else if ($n['type'] == 3) {
			$senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
			echo $senderName." commented on your post!<hr>";
		}
	}
}
?>