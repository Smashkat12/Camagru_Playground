<?php
include('./classes/dbh.php');
include('./classes/login.inc.php');
include('./classes/post.inc.php');
include('./classes/comment.inc.php');

$showTimeline = false;
if (Login::isLoggedin()) {
	$userid = Login::isLoggedin();
	$showTimeline = true;
} else {
	echo "Not Logged in";
}
if (isset($_GET['postid2'])) {
	Post::likePost($_GET['postid2'], $userid);
}
if (isset($_POST['comment'])) {
	Comment::createComment($_POST['commentbody'], $_GET['postid'],$userid);
}
//Show the timeline
$followingposts = DB::query('SELECT posts.id, posts.body, posts.posted_at, posts.likes, users.`username` FROM users, posts, followers WHERE posts.user_id = followers.user_id AND users.id = posts.user_id AND follower_id =:userid ORDER BY posts.posted_at DESC;', array(':userid'=>$userid));
foreach($followingposts as $post) {
		echo $post['body']." ~ ".$post['username'];
		echo "<form action='index.php?postid2=".$post['id']."' method='post'>";
		if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$userid))) {
			echo "<input type='submit' name='like' value='Like'>";
		} else {
			echo "<input type='submit' name='unlike' value='Unike'>";
		}
		echo "<span>".$post['likes']." likes</span>
		</form>
		<form action='index.php?postid=".$post['id']."' method='post'>
		<textarea name='commentbody' cols='50' rows='3'></textarea>
		<input type='submit' name='comment' value='Comment'>
		</form>
		";
		Comment::displayComment($post['id']);
		echo "
		<hr /></br />";

		
}
