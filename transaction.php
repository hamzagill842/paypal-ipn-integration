<?php
include_once 'config.php';

// Replace with your actual PayPal sandbox credentials
$clientID = PAYPAL_Client_ID;
$secret = PAYPAL_Client_SERECT;

// PayPal API base URL
$paypalApiBaseUrl = PAYPAL_API_URL;

// Function to obtain an access token
function getAccessToken($clientID, $secret) {
    global $paypalApiBaseUrl;

    $url = $paypalApiBaseUrl . '/v1/oauth2/token';
    $credentials = base64_encode("$clientID:$secret");

    $headers = array(
        'Authorization: Basic ' . $credentials,
        'Content-Type: application/x-www-form-urlencoded',
    );

    $data = 'grant_type=client_credentials';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $jsonResponse = json_decode($response, true);

    return $jsonResponse['access_token'];
}

// Function to get transaction details by ID
function getTransactionDetails($accessToken, $transactionId) {
    global $paypalApiBaseUrl;

    // Set start and end dates to today
    $startDate = date('Y-m-d', strtotime('-1 day')) . 'T00:00:00-0000';
    $endDate = date('Y-m-d') . 'T23:59:59-0000';

    // PayPal Payments API endpoint
    $url = "$paypalApiBaseUrl/v1/reporting/transactions?" .
        "start_date=$startDate&end_date=$endDate&page=1&page_size=1&fields=all&transaction_id=$transactionId";

    // cURL options for the GET request
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        ),
        CURLOPT_RETURNTRANSFER => true,
    );

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt_array($ch, $options);

    // Execute the GET request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    } else {
        // Handle the response as needed
        $jsonResponse = json_decode($response, true);

        return $jsonResponse;
    }

    // Close cURL session
    curl_close($ch);
}

// Replace with your actual transaction ID
$transactionId = '6N8009973Y725505T';

// Replace with your actual access token
$accessToken = getAccessToken($clientID, $secret);

echo $accessToken;
// Call the function to get transaction details
$data = getTransactionDetails($accessToken, $transactionId);

?>


