<?php
function send_firebase($token, $message, $single = TRUE)
{
	$url = 'https://fcm.googleapis.com/fcm/send';

	$field = [
		'to'        => $token,
		'data' => $message
	];

	if($single == TRUE){
		$field = array(
			'to'=>$token,
			'data'=>$message
		);
	}else{
		$field = array(
			'registration_ids'=>$token,
			'data'=>$message
		);
	}


		// AIzaSyA1IYi3e4WIFyWGu7mmE12tCyEwD1qX8Sg
		// AIzaSyDDc_6WqtA0xFEwPVsNOq19n1Bz0OFZa4U
		// AIzaSyB51tOqTWGicD8qEji3oPlULG1oNSEUjfo
	$headers = array(
		/* Key didapat dari project di website firebase */
		'Authorization:key = AIzaSyB51tOqTWGicD8qEji3oPlULG1oNSEUjfo',
		'Content-Type: application/json'
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($field));

	$result = curl_exec($ch);
	if($result === FALSE){
		die('Curl failed: '. curl_error($ch));
	}

	curl_close($ch);
	return $result;
}
?>
