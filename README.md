# Swisspost API PHP
PHP example Script for a call to Digital Commerce API from swisspost 
 
Manual here: https://developer.post.ch/en/digital-commerce-api
Call to https://wedec.post.ch
 
Change 2 Parameter for use:
$client_secret = 'TEST_XXXXXXXXX';
$client_id = 'TEST_XXXXXXXXXXX';
 
I take a demodata for ask Cityname with zip as a parameter	

$scope = "WEDEC_AUTOCOMPLETE_ADDRESS";		
$parameters = "zipCity=4586&type=DOMICILE"; 
 
