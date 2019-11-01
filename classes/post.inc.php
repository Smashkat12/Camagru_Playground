<?php

class Post
{
	public static function createPost($postbody, $loggedInUserId, $profileUserId)
	{
		if (strlen($postbody) < 1 || strlen($postbody) > 160) {
			die('Incorrect Length of text: 1 - 160 chars requred');
		}

		if ($loggedInUserId == $profileUserId) {
			DB::query('INSERT INTO posts VALUES (NULL, :postbody, NOW(), :userid, 0, NULL)', array(':postbody' => $postbody, ':userid' => $profileUserId));
		} else {
			die('Incorrect user!: Cant post on other users page');
		}
	}
	public static function createImgPost($postbody, $loggedInUserId, $profileUserId)
	{
		if (strlen($postbody) > 160) {
			die('Incorrect Length of text: 1 - 160 chars requred');
		}

		if ($loggedInUserId == $profileUserId) {
			DB::query('INSERT INTO posts VALUES (NULL, :postbody, NOW(), :userid, 0, NULL)', array(':postbody' => $postbody, ':userid' => $profileUserId));
			$postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY id DESC LIMIT 1', array(':userid'=>$loggedInUserId))[0]['id'];
			return $postid;
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

	public static function link_add($text) {
		$text = explode(" ", $text);
		$newstring = "";
		foreach ($text as $word) {
			if (substr($word, 0, 1) == "@") {
				$newstring .= "<a href='profile.php?username=".substr($word, 1)."'>".htmlspecialchars($word)." </a>";
			} else {
				$newstring .= htmlspecialchars($word)." ";
			}
			
		}
		return $newstring;
	}
	public static function displayPosts($userid, $username, $loggedInUserId) {
		//display all posts ordered by newest on top
		$dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid' => $userid));
		$posts = "";
		foreach ($dbposts as $p) {
			//check if user has already liked the post
			if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))) {
				//post are protected from parsing html
				$posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
				<form action='profile.php?username=$username&postid=" . $p['id'] . "' method='post'>
					<input type='submit' name='like' value='Like'>
					<span>".$p['likes']." likes</span>
				";
				if ($userid == $loggedInUserId) {
					$posts .= "<input type='submit' name='deletepost' value='x' />";
				}
				$posts .= "
				</form><hr /></br />
				";
			} else {
				$posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
				<form action='profile.php?username=$username&postid=" . $p['id'] . "' method='post'>
					<input type='submit' name='unlike' value='Unlike'>
					<span>".$p['likes']." likes</span>
				";
				if ($userid == $loggedInUserId) {
					$posts .= "<input type='submit' name='deletepost' value='x' />";
				}
				$posts .= "
				</form><hr /></br />
				";
			}
		}
		return $posts;
	}
}
