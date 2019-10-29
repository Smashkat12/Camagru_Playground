<?php
include('./classes/dbh.php');
include('./classes/login.inc.php');
$showTimeline = false;
if (Login::isLoggedin()) {
	$showTimeline = true;
} else {
	echo "Not Logged in";
}
//Show the timeline
$followingposts = DB::query('SELECT posts.body, posts.likes, users.`username` FROM users, posts, followers WHERE posts.user_id = followers.user_id AND users.id = posts.user_id AND follower_id = 7 ORDER BY posts.likes DESC;');
foreach($followingposts as $post) {
	echo $post['body']." ~ ".$post['username']."<hr />";
}

?>