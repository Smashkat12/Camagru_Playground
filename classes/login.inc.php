<?php
class Login {
	//check if user is logged in by checking if cookie has been set
	public static function isLoggedin() {
		if (isset($_COOKIE['CMGRUID'])) {
			if (DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CMGRUID'])))) {
				$user_id = DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CMGRUID'])))[0]['user_id'];
				if (isset($_COOKIE['CMGRUID_2'])) {
					return $user_id;
				} else {
					//ask server for a new token
					$crypto_strong = True;
					$token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
					DB::query('INSERT INTO login_tokens VALUES (NULL, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
					DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['CMGRUID'])));
	
					setcookie("CMGRUID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, true);
					setcookie("CMGRUID_2", 1, time() + 60 * 60 * 24 * 3, '/', NULL, NULL, true);
	
					return $user_id;
				}
			}
		}
		return false;
	}
}
?>