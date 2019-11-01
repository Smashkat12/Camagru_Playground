<?php

class Post
{
	public static function createPost($postbody, $loggedInUserId, $profileUserId)
	{
		if (strlen($postbody) < 1 || strlen($postbody) > 160) {
			die('Incorrect Length of text: 1 - 160 chars requred');
		}

		if ($loggedInUserId == $profileUserId) {
			DB::query('INSERT INTO posts VALUES (NULL, :postbody, NOW(), :userid, 0)', array(':postbody' => $postbody, ':userid' => $profileUserId));
		} else {
			die('Incorrect user!: Cant post on other users page');
		}
	}
	public static function likePost ($postid, $likerId) {
		//check if user has already liked the post or not
		if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $postid, ':userid' => $likerId))) {
			DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid' => $postid));
			//tell us which user liked the post
			DB::query('INSERT INTO post_likes VALUES (NULL, :postid, :userid)', array(':postid' => $postid, ':userid' => $likerId));
		} else {
			DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid' => $postid));
			//tell us which user liked the post
			DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postid, ':userid'=> $likerId));
		}
	}
	public static function displayPosts($userid, $username, $loggedInUserId) {
		//display all posts ordered by newest on top
		$dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid' => $userid));
		$posts = "";
		foreach ($dbposts as $p) {
			//check if user has already liked the post
			if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))) {
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
		return $posts;
	}
}
