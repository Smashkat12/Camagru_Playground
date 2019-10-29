<?php
include('./classes/dbh.php');
include('./classes/login.inc.php');

$username = "";
$verified = false;
$isFollowing = false;
if (isset($_GET['username'])) {
	if (DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $_GET['username']))) {
		$username = DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['username'];
		//represent the person whose page were are on
		$userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['id'];
		$verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['verified'];
		//represents the person who is logged in
		$followerid = Login::isLoggedin();



		if (isset($_POST['follow'])) {
			if ($userid != $followerid) {
				if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid))) {
					//check for Official verified acc
					if ($followerid == 7) {
						DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid' => $userid));
					}
					DB::query('INSERT INTO followers VALUES (NULL, :userid, :followerid)', array(':userid' => $userid, ':followerid' => $followerid));
				} else {
					echo "Already following!";
				}
				$isFollowing = true;
			}
		}
		if (isset($_POST['unfollow'])) {
			if ($userid != $followerid) {
				if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid))) {
					//check for Official verified acc
					if ($followerid == 7) {
						DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid' => $userid));
					}
					DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid));
				}
				$isFollowing = false;
			}
		}
		if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userid, ':followerid' => $followerid))) {
			//echo "Already following!";
			$isFollowing = true;
		}
		//Insert posts into DB
		if (isset($_POST['post'])) {
			$postbody = $_POST['postbody'];
			$loggedInUserId = Login::isLoggedin();

			if (strlen($postbody) < 1 || strlen($postbody) > 160) {
				die('Incorrect Length of text: 1 - 160 chars requred');
			}

			if ($loggedInUserId == $userid) {
				DB::query('INSERT INTO posts VALUES (NULL, :postbody, NOW(), :userid, 0)', array(':postbody' => $postbody, ':userid' => $userid));
			} else {
				die('Incorrect user!: Cant post on other users page');
			}
		}
		//Post LIKES
		if (isset($_GET['postid'])) {
			//check if user has already liked the post or not
			if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $_GET['postid'], ':userid' => $followerid))) {
				DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid' => $_GET['postid']));
				//tell us which user liked the post
				DB::query('INSERT INTO post_likes VALUES (NULL, :postid, :userid)', array(':postid' => $_GET['postid'], ':userid' => $followerid));
			} else {
				DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid' => $_GET['postid']));
				//tell us which user liked the post
				DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
			}
		}
		//display all posts ordered by newest on top
		$dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid' => $userid));
		$posts = "";
		foreach ($dbposts as $p) {
			//check if user has already liked the post
			if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$followerid))) {
				//post are protected from parsing html
				$posts .= htmlspecialchars($p['body']) . "
				<form action='profile.php?username=$username&postid=" . $p['id'] . "' method='post'>
					<input type='submit' name='like' value='Like'>
					<span>".$p['likes']." likes</span>
				</form>
				<hr /></br />
				";
			} else {
				$posts .= htmlspecialchars($p['body']) . "
				<form action='profile.php?username=$username&postid=" . $p['id'] . "' method='post'>
					<input type='submit' name='unlike' value='Unlike'>
					<span>".$p['likes']." likes</span>
				</form>
				<hr /></br />
				";
			}
		}
	} else {
		die('User not found!');
	}
}
?>
<h1><?php echo $username; ?>'s Profile<?php if ($verified) {
											echo '- Verified';
										} ?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
	<?php
	if ($userid != $followerid) {
		if ($isFollowing) {
			echo '<input type="submit" name="unfollow" value="Unfollow">';
		} else {
			echo '<input type="submit" name="follow" value="Follow">';
		}
	}
	?>
</form>

<form action="profile.php?username=<?php echo $username; ?>" method="post">
	<textarea name="postbody" cols="80" rows="8"></textarea>
	<input type="submit" name="post" value="Post">
</form>

<div class="posts">
	<?php echo $posts; ?>
</div>