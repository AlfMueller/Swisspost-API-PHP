<?php

/****************************************************************
*                                                               *
* Democode für API Swisspost (PHP V7.4)				*
*                                                               *
* Author: Alf Müller - bastelgarage.ch - purecrea gmbh		*
* Date: 8.2.2021						*
*                                                               *
*****************************************************************/


 $client_secret = 'TEST_XXXXXXXXX'; 		
 $client_id = 'TEST_XXXXXXXXXXX';

$scope = "WEDEC_AUTOCOMPLETE_ADDRESS";
$parameters = "zipCity=4586&type=DOMICILE";


// ab hier nichts mehr ändern

//adress
$authorize_url = "https://wedec.post.ch/WEDECOAuth/authorization";
$token_url = "https://wedec.post.ch/WEDECOAuth/token";


//	Callback-URL, die bei der Definition der Anwendung angegeben wurde - muss mit dem übereinstimmen, was die Anwendung sagt
$callback_uri = ((empty($_SERVER['HTTPS'])) ? 'http' : 'https') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$test_api_url = "https://wedec.post.ch/api/address/v1/zips?".$parameters;

function getAuthorizationCode() {
	global $authorize_url, $client_id, $callback_uri;

	$authorization_redirect_url = $authorize_url . "?response_type=code&client_id=" . $client_id . "&redirect_uri=" . $callback_uri . "&scope=".$scope;

	header("Location: " . $authorization_redirect_url);
}
//echo $posttoken;

//	Schritt I, J - den Autorisierungscode in ein Zugriffstoken umwandeln, usw.
function getAccessToken() {
	global $token_url, $client_id, $client_secret, $callback_uri, $scope;

	//$authorization = base64_encode("$client_id:$client_secret");
	//$header = array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded");
	$content = "grant_type=client_credentials&client_id=$client_id&client_secret=$client_secret&redirect_uri=$callback_uri&scope=$scope";

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => $token_url,
		CURLOPT_HTTPHEADER => $header,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $content,
		CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            )
	));
	$response = curl_exec($curl);
	curl_close($curl);

	if ($response === false) {
		echo "Failed";
		echo curl_error($curl);
		echo "Failed";
	} elseif (json_decode($response)->error) {
		echo "Error:<br />";
		echo $response;
	}

	return json_decode($response)->access_token;
}

$token = getAccessToken();
//	wir können nun das access_token beliebig oft für den Zugriff auf geschützte Ressourcen verwenden
function getResource($token) {
	global $test_api_url;
	
$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL =>$test_api_url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_HTTPHEADER => array(
    	'Authorization: Bearer ' . $token
  	),
));

	$response = curl_exec($curl);
	curl_close($curl);
	echo $response;

}

if(isset($_GET['token'])) {
	echo getResource($_GET['token']);
}else{
	$token = getAccessToken();
	header('Location: apicall.php?token='.$token);
}

?>

