<?php
require_once("ApplePushSender.php");
// Put your device token here (without spaces):
// Such as the following sample
//$deviceToken = '0f744707bebcf74f9b7c25d48e3358945f6aa01da5ddb387462c7eaf61bbad78';
//$deviceToken = '70757ff293087502f7fc33816a0e453486782b12a90e72ac3e2a48b43cdf807f';
//Sinri trail for array of deviceTokens
$deviceTokenArray=array(
	"SINRiPAD"=>'ac1977c00591ecf43fa5dfe893c414480b889dbe728f4e0249c8d7f72bb1d3e2',
	);
/*
// Put your private key's passphrase here:
$passphrase = 'leqeeleqee';
// Put the PEM file (contains cert & key) here:
$CKPemFile = 'PushLibDuoCK.pem';
*/
// Put your alert message_alert_type and addition(optional) here:
//$message_alert_type = 'PSALMS 91:1';
//$message_addition = 'He that dwelleth in the secret place of the most High shall abide under the shadow of the Almighty.';

if($_REQUEST['action']=='aps'){
	$posted_deviceTokenArray=explode(',',$_REQUEST['deviceTokens']);
	/*
	if($posted_deviceTokenArray && !empty($posted_deviceTokenArray)){
		$deviceTokenArray=$posted_deviceTokenArray;
	}
	*/
	$message_alert_type=$_REQUEST['alert'];
	$message_addition=$_REQUEST['addition'];
	$result=ApplePushSenderToDevices($posted_deviceTokenArray,$message_alert_type,$message_addition);
}

////////////////////////////////////////////////////////////////////////////////
?>
<html>
<head>
	<title>Apple Push Sender Page</title>
	<script type="text/javascript">
	function changeOnDT(DT){
		var DTTA=document.getElementById('deviceTokensTA');
		if(DTTA){
			var old=DTTA.innerHTML;
			if(old.indexOf(DT)>=0){
				old=old.replace(DT+",","");
				old=old.replace(DT,"");
			}else{
				if(old.length>0){
					old=old+",";
				}
				old=old+DT;
			}
			old=old.replace(",,",",");
			DTTA.innerHTML=old;
		}
	}
	</script>
</head>
<body>
	<fieldset>
		<legend>Apple Push Sender Page - LeqeePushLib</legend>
		<div>
			<fieldset>
				<legend>ALL REGISTERED DEVICE TOKENS</legend>
				<?php
				foreach ($deviceTokenArray as $device_tag => $theDeviceToken) {
					echo "<p>";
					echo "<input type='checkbox' name='addDTCB' value='add to send list' onclick='changeOnDT(\"".$theDeviceToken."\")'>";
					echo "[".$device_tag."] ".$theDeviceToken;
					echo "</p>";
				}
				?>
			</fieldset>
		</div>
		<br>
		<div>
			<form method="POST">
				<div style="display:block;">
					<textarea name="deviceTokens" id="deviceTokensTA"><?php
						//echo implode(",",$deviceTokenArray);
					?></textarea>
				</div>
				<fieldset>
					<legend>ALERT</legend>
					<input name="alert" style="width:100%;">
				</fieldset>
				<br>
				<fieldset>
					<legend>ADDITION</legend>
					<textarea name="addition" style="width:100%; height:50px;"></textarea>
				</fieldset>
				<br>
				<p style="text-align:right;">
					<input type="submit" style="width:25%;">
					<input type="hidden" name="action" value="aps">
				</p>
			</form>
		</div>
	</fieldset>
	<hr>
	<div>
		<?php
		if($result){
			foreach ($result as $line_number => $line) {
				echo "<p>[$line_number] $line</p>";
			}
		}
		?>
	</div>
</body>
</html>