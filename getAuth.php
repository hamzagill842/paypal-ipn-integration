<?php
require __DIR__ . '/vendor/autoload.php';
include_once 'config.php';
include_once 'googleConfig.php';

// Replace 'YOUR_CLIENT_ID' and 'YOUR_CLIENT_SECRET' with your actual values

$authUrl = $client->createAuthUrl();


function redirect($url) {
    header("Location: $url");
    exit;
}

redirect($authUrl);



