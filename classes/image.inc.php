<?php
class Image {
	public static function uploadImage($formname, $query, $params) {
		//return encoded image as string
	$image = base64_encode(file_get_contents($_FILES[$formname]['tmp_name']));
	//create stream
	$options = array('http'=>array(
		'method'=>"POST",
		'header'=>"Authorization: Bearer 8f071a0c68940eb80440a1315f56c5d0b5657ca2\n".
		"Content-Type: application/x-www-form-urlencoded",
		'content'=>$image
	));

	$context = stream_context_create($options);

	$imgurURL = "https://api.imgur.com/3/image";
	//check if size of image is less then 10mb
	if ($_FILES[$formname]['size'] > 10240000) {
		die('Image size too big, must be 10mb or less!');
	}
	//open img file ussing HTTP headers set when creating stream
	$response = file_get_contents($imgurURL, false, $context);
	//return json object
	$response = json_decode($response);
	
	$preparams = array($formname=>$response->data->link);
	$params = $preparams + $params;

	DB::query($query, $params);
	}
}
?>