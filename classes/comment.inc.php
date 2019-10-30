<?php

class Comment {
	public static function createComment($commentBody, $postId, $userId)
	{
		if (strlen($commentBody) < 1 || strlen($commentBody) > 160) {
			die('Incorrect Length of text: 1 - 160 chars requred');
		}
		//check if the post still exists
		if (!DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$postId))) {
			echo 'Invalid post ID';
		} else {
			//insert post into Db
			DB::query('INSERT INTO comments VALUES (NULL, :comment, :userid, NOW(), :postid)', array(':comment'=>$commentBody, ':userid'=>$userId, ':postid'=>$postId));
		}
	}

	public static function displayComment($postId) {
		$comments = DB::query('SELECT comments.comment, users.username FROM comments, users WHERE post_id=:postid AND comments.user_id=users.id', array(':postid'=>$postId));
		foreach($comments as $comment) {
			echo $comment['comment']." ~ ".$comment['username']."<p></p>";
			
		}
	}
}
