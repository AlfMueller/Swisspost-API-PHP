<?php

/*******************************************
 * Author: Alf Müller (purecrea gmbh)
 * Date: 2023-06-30
 
 
 ZPL file test online: 
 http://labelary.com/viewer.html (100x150mm 300dpi)

 * This document provides a SwissPostAPI class that allows you to generate an address label barcode
 * using the Swiss Post API. The class handles the authentication process, sends a POST request to the API,
 * and retrieves the generated barcode label data in ZPL format. The ZPL string is then displayed and can be
 * saved as a .zpl file. Additionally, the comment provides instructions on how to test the generated ZPL file
 * using the Labelary ZPL Viewer and how to send the ZPL file to a Zebra printer for printing the barcode label.
 *
 * To use this document, replace the "XXXXX" placeholders with your actual client ID and client secret,
 * and then create an instance of the SwissPostAPI class to generate the barcode label.
 */


$client_id          = "XXXXX";
$client_secret      = "XXXXX";

class SwissPostAPI {
    private $apiUrl = 'https://dcapi.apis.post.ch/barcode/v1/generateAddressLabel';
    private $client_id;
    private $client_secret;
    private $accessToken;
            
    public function __construct($client_id, $client_secret) {
        $this->clientId = $client_id ;
        $this->clientSecret = $client_secret;
        $this->authenticate();
    }
       /**
     * Authenticates the client using the provided client ID and client secret.
     */
	
    private function authenticate() {
        $tokenUrl = 'https://api.post.ch/OAuth/token';
        $postData = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => 'DCAPI_BARCODE_READ' // Replace with the correct scope
        );
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $tokenUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);

        $responseData = json_decode($response, true);


        if (isset($responseData['access_token'])) {
            $this->accessToken = $responseData['access_token'];
        } else {
            // Handle authentication error
            // You can customize this part based on your needs
            echo 'Authentication error: ' . $response;
        }
    }
	
	
	    /**
     * Creates a barcode by sending a POST request to the Swiss Post API.
     */
    
    public function createBarcode() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://dcapi.apis.post.ch/barcode/v1/generateAddressLabel',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "language": "DE",
            "frankingLicense": "xxxxxxxxx", // your Licence here
            "ppFranking": false,
            "customer": {
                "name1": "Purecrea GmbH",
                "name2": "bastelgarage.ch",
                "street": "Gewebestrasse 23",
                "zip": "4556",
                "city": "Subingen",
                "country": "CH",
                "logoRotation": 0,
                "domicilePostOffice": "4586 Subingen"
            },
            "customerSystem": null,
            "labelDefinition": {
                "labelLayout": "A6",
                "printAddresses": "RECIPIENT_AND_CUSTOMER",
                "imageFileType": "ZPL2",
                "imageResolution": 300,
                "printPreview": false
            },
            "item": {
                "itemID": "00000000001000727746",
                "recipient": {
                    "title": "Frau",
                    "name1": "Serena Muster",
                    "street": "Teststrasse 11",
                    "mailboxNo": null,
                    "zip": "9000",
                    "city": "St.Gallen",
                    "country": "CH",
                    "houseKey": "24",
                    "email": "test@test.test"
                },
                "attributes": {
                    "przl": [
                        "PRI"
                    ],
                    "deliveryDate": null,
                    "returnInfo": {
                        "returnNote": false,
                        "instructionForReturns": false,
                        "returnService": null,
                        "customerIDReturnAddress": null
                    },
                    "weight": 1
                }
            }
        }',
          CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ),
));

$response = curl_exec($curl);

curl_close($curl);
$labelData = json_decode($response, true);
$zplString = base64_decode($labelData['item']['label'][0]);
$zplStringout = str_replace("^", "<br>^", $zplString);
echo ("^XA". $zplStringout. "^XZ"); 
$zplString = "^XA". $zplString. "^XZ"; 

// Save ZPL string to a file
$date = date("Y-m-d");
$fileName = "barcode_$date.zpl";
 file_put_contents($fileName, $zplString);
    }
}



// Example usage
$api = new SwissPostAPI($client_id, $client_secret);
$response = $api->createBarcode();


?>
