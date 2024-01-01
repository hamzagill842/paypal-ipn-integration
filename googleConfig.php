<?php

require __DIR__ . '/vendor/autoload.php';
include_once 'config.php';

// Set the path to your credentials.json file
//$credentialsPath = '/credentials.json';

// Create the Google Sheets service
$clientID = GOOGLE_Client_ID;
$clientSecret = GOOGLE_Client_SERECT;
$redirectUri = GOOGLE_REDIRECT_URI.'/paypal_google_sheet/googleRedirect.php';  // Redirect URI configured in Google Cloud Console




// Create the Google Sheets service
$client = new Google_Client();
$client->setApplicationName('Your Application Name');
$client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->setAccessType('offline');
