<?php
// Put your private key's passphrase here:
$passphrase = 'leqeeleqee';
// Put the PEM file (contains cert & key) here:
$CKPemFile = 'BabyNesPosProductDuoCK.pem';

$APPLE_PUSH_GATEWAY_Sandbox='ssl://gateway.sandbox.push.apple.com:2195';
$APPLE_PUSH_GATEWAY_Production='ssl://gateway.push.apple.com:2195';

////////////////////////////////////////////////////////////////////////////////

function ApplePushSenderToOneDevice($isSandBox,$theDeviceToken,$theAlert,$theAddition,$act){

	global $passphrase;
	global $CKPemFile;

	global $APPLE_PUSH_GATEWAY_Sandbox;
	global $APPLE_PUSH_GATEWAY_Production;

	$history=array();

	if(!$theDeviceToken || empty($theDeviceToken)){
		$history[]='Device Token is empty';
		return $history;
	}

	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', $CKPemFile);
	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

	if($isSandBox){
		$gateway=$APPLE_PUSH_GATEWAY_Sandbox;
	}else{
		$gateway=$APPLE_PUSH_GATEWAY_Production;
	}

	// Open a connection to the APNS server
	$fp = stream_socket_client(
		$gateway,
		$err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

	if (!$fp)
		exit("Failed to connect: $err $errstr" . PHP_EOL);

	//echo 'Connected to APNS' . PHP_EOL;
	$history[]='Connected to APNS';

	// Create the payload body
	$body['aps'] = array(
		'act' => $act,
		'alert' => $theAlert,
		'sound' => 'default',
		'addition' => $theAddition
		);

	// Encode the payload as JSON
	$payload = json_encode($body);

	$history[]='JSON='.$payload;

	// Build the binary notification, FOR ONE device
	$msg = chr(0) . pack('n', 32) . pack('H*', $theDeviceToken) . pack('n', strlen($payload)) . $payload;

	// Send it to the server
	$result = fwrite($fp, $msg, strlen($msg));

	if (!$result){
		//echo 'Message not delivered' . PHP_EOL;
		$history[]='Message not delivered to '.$theDeviceToken;
	}
	else{
		//echo 'Message successfully delivered' . PHP_EOL;
		$history[]='Message successfully delivered to '.$theDeviceToken;
	}

	// Close the connection to the server
	fclose($fp);

	return $history;
}

function ApplePushSenderToDevices($isSandBox,$theDeviceTokenArray,$theAlert,$theAddition,$act){

	global $passphrase;
	global $CKPemFile;

	global $APPLE_PUSH_GATEWAY_Sandbox;
	global $APPLE_PUSH_GATEWAY_Production;

	$history=array();

	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', $CKPemFile);
	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

	if($isSandBox){
		$gateway=$APPLE_PUSH_GATEWAY_Sandbox;
	}else{
		$gateway=$APPLE_PUSH_GATEWAY_Production;
	}

	// Open a connection to the APNS server
	$fp = stream_socket_client(
		$gateway,
		$err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

	if (!$fp){
		$history[]="Failed to connect: $err $errstr";
		//exit("Failed to connect: $err $errstr" . PHP_EOL);
		return $history;
	}

	//echo 'Connected to APNS' . PHP_EOL;
	$history[]='Connected to APNS';

	// Create the payload body
	$body['aps'] = array(
		'act' => $act,
		'alert' => $theAlert,
		'sound' => 'default',
		'addition' => $theAddition
		);

	// Encode the payload as JSON
	$payload = json_encode($body);

	$history[]='JSON='.$payload;

	foreach ($theDeviceTokenArray as $device_tag => $theDeviceToken) {
		if(!$theDeviceToken || empty($theDeviceToken)){
			continue;
		}

		// Build the binary notification, FOR ONE device
		$msg = chr(0) . pack('n', 32) . pack('H*', $theDeviceToken) . pack('n', strlen($payload)) . $payload;

		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));

		if (!$result){
			//echo 'Message not delivered' . PHP_EOL;
			$history[]='Message not delivered to '.$theDeviceToken;
		}
		else{
			//echo 'Message successfully delivered' . PHP_EOL;
			$history[]='Message successfully delivered to '.$theDeviceToken;
		}
	}
	// Close the connection to the server
	fclose($fp);

	return $history;
}
?>