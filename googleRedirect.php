<?php

include_once 'googleConfig.php';
include_once 'config.php';



if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Exchange authorization code for access and refresh tokens
    $Tokens = $client->fetchAccessTokenWithAuthCode($code);
    echo var_dump($Tokens);
    $access_token = $Tokens['access_token'];
    echo '   access   '. $access_token;
    $refreshToken = isset($Tokens['refresh_token']) ? $Tokens['refresh_token'] : null;
    echo '  refresh '.$refreshToken;
    $expiresIn = 3600;
    $service = 'google';

// Check if the access token already exists in the database
    $stmtCheck = $conn->prepare("SELECT id FROM tokens WHERE service = ?");
    $stmtCheck->bind_param("s", $service);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        // Update the existing access token
        $stmtUpdate = $conn->prepare("UPDATE tokens SET access_token = ?, refresh_token = ?, expires_in = ?, created_at = NOW() WHERE service = ?");
        $stmtUpdate->bind_param("ssis", $access_token, $refreshToken, $expiresIn, $service);
        $stmtUpdate->execute();

        echo "Token successfully updated in the database.";
    } else {
        // Insert a new access token
        $stmtInsert = $conn->prepare("INSERT INTO tokens (service, access_token, refresh_token, expires_in, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmtInsert->bind_param("sssi", $service, $access_token, $refreshToken, $expiresIn);
        $stmtInsert->execute();

        echo "Token successfully created in the database.";
    }

// Close the statements
    $stmtCheck->close();
    if (isset($stmtUpdate)) {
        $stmtUpdate->close();
    }
    if (isset($stmtInsert)) {
        $stmtInsert->close();
    }

} else {
    echo "Authorization code not received.";
}


