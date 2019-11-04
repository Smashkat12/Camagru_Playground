<?php
class notify {
	//handles notifications
	public static function createNotification($text) {
		$text = explode(" ", $text);
		$notify = array();
		foreach ($text as $word) {
			if (substr($word, 0, 1) == "@") {
				$notify[substr($word, 1)] = array("type"=>1, "extra"=>' { "postbody":"'.htmlentities(implode(" ", $text)).'"}');	
			} 
		}
		return $notify;
	}
}
?>