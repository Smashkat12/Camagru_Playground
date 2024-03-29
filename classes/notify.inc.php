<?php
class notify
{
	//handles notifications
	public static function createNotification($text = "", $postid = 0)
	{
		$notify = array();
		//Like notifications
		if ($text == NULL && $postid != 0) {
			$temp = DB::query('SELECT posts.user_id AS receiver, post_likes.user_id AS sender FROM posts, post_likes WHERE posts.id = post_likes.post_id AND posts.id=:postid', array(':postid' => $postid));
			$r = $temp[0]["receiver"];
			$s = $temp[0]["sender"];
			if ($r != 0) {
				DB::query('INSERT INTO notifications VALUES (NULL, :type, :receiver, :sender, :extra)', array(':type' => 2, ':receiver' => $r, ':sender' => $s, ':extra' => NULL));
				// checkif user is still opted to receive email notifications
				$send_email = DB::query('SELECT email_consent FROM users WHERE id = :receiver', array(':receiver'=>$r));
				if ($send_email[0]['email_consent']) {
					$email = DB::query('SELECT email FROM users WHERE id = :receiver', array(':receiver'=>$r))[0]['email'];
					$s_username = DB::query('SELECT * FROM users WHERE id = :liker', array(':liker'=>$s))[0]['username'];
					$to = $email;
					$subject = "Post Likes";
					$message = "<p> $s_username liked your post!</p>";
					$headers = "From: camagru <admin@camagru.com>\r\n";
					$headers .= "Reply-To: camagru <admin@camagru.com>\r\n";
					$headers .= "Content-type: text/html\r\n";
					mail($to, $subject, $message, $headers);
				}
			}
		} else {

			$text = explode(" ", $text);

			foreach ($text as $word) {
				//@mention
				if (substr($word, 0, 1) == "@") {
					$notify[substr($word, 1)] = array("type" => 1, "extra" => ' { "postbody":"' . htmlentities(implode(" ", $text)) . '"}');
				}
			}
			$temp = DB::query('SELECT posts.user_id AS receiver, comments.user_id AS sender FROM posts, comments WHERE posts.id = comments.post_id AND posts.id=:postid', array(':postid' => $postid));
			if (isset($temp[0])) {
				$r = $temp[0]["receiver"];
				$s = $temp[0]["sender"];
				//DB::query('INSERT INTO notifications VALUES (NULL, :type, :receiver, :sender, NULL)', array(':type'=>2, ':receiver'=>$r, ':sender'=>$s));
				if ($r != 0) {
					DB::query('INSERT INTO notifications VALUES (NULL, :type, :receiver, :sender, :extra)', array(':type' => 3, ':receiver' => $r, ':sender' => $s, ':extra' => NULL));
					$send_email = DB::query('SELECT email_consent FROM users WHERE id = :receiver', array(':receiver'=>$r));
					if ($send_email[0]['email_consent']) {
						$email = DB::query('SELECT email FROM users WHERE id = :receiver', array(':receiver'=>$r))[0]['email'];
						$s_username = DB::query('SELECT * FROM users WHERE id = :liker', array(':liker'=>$s))[0]['username'];
						$to = $email;
						$subject = "Post Comments";
						$message = "<p> $s_username commented on your post!</p>";
						$headers = "From: camagru <admin@camagru.com>\r\n";
						$headers .= "Reply-To: camagru <admin@camagru.com>\r\n";
						$headers .= "Content-type: text/html\r\n";
						mail($to, $subject, $message, $headers);
					}
				}
			}
		}
		return $notify;
	}
}