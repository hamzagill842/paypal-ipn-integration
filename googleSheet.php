<?php
require __DIR__ . '/vendor/autoload.php';
include_once 'googleConfig.php';
include_once 'config.php';


function getAccessTokenFromDatabase($conn)
{
    try {

        $stmt = $conn->query("SELECT access_token, refresh_token FROM tokens ORDER BY created_at DESC LIMIT 1");


        if ($stmt) {
            $result = $stmt->fetch_assoc();
            $stmt->close();
            return $result;
        }

    } catch (Exception $e) {
        // Handle other exceptions
        echo "Error: " . $e->getMessage();
        return null;
    }
}



$tokens = getAccessTokenFromDatabase($conn);
$client->setAccessToken($tokens['access_token']);
// Set the access token in the Google API client
if ($client->isAccessTokenExpired()) {
    // Refresh the access token

    $newAccessToken = $client->fetchAccessTokenWithRefreshToken($tokens['refresh_token']);

    // Update the database with the new access token
    updateAccessTokenInDatabase($newAccessToken);
} else {
    echo "Access token is still valid.\n";
}

$service = new Google_Service_Sheets($client);
// ID of the Google Sheet
$spreadsheetId = GOOGLE_SHEET_ID;


// Range where you want to add data (e.g., Sheet1!A1:Q1)
$range = 'PayPal_IPN!A1:Q1';

//$transaction_subject = (isset($_POST['transaction_subject']) && $_POST['transaction_subject'] != null) ? $_POST['transaction_subject']: $product_name;


 foreach ($cartItems as $item) {
     $rowData = [[
         $year,
         $txn_type,
         $date,
         $time,
         $payer_first_name . ' ' . $payer_last_name,
         $payer_email,
         $item['quantity'],
         $item['name'],
         $item['amount'],
         $shipping,
         '',
         $address_street,
         '',
         $address_zip,
         $address_city,
         $address_country,
         $transactionID,
         $item['parent_txn_id']
     ]
     ];

     // Create a ValueRange object
     $values = new Google_Service_Sheets_ValueRange([
         'values' => $rowData,
     ]);

     // Set the major dimension to 'ROWS'
     $values->setMajorDimension('ROWS');

     // Call the Sheets API to append the data
     $params = [
         'valueInputOption' => 'USER_ENTERED',
     ];
     $result = $service->spreadsheets_values->append($spreadsheetId, $range, $values, $params);

     if (empty($values)) {
         print "No data found.\n";
     } else {
         print 'new row add in the sheet.';
     }
 }



function updateAccessTokenInDatabase($newAccessToken)
{
    // Assuming you have a database connection stored in $conn
    global $conn;

    $access_token = $newAccessToken['access_token'];
    $refreshToken = isset($newAccessToken['refresh_token']) ? $newAccessToken['refresh_token'] : null;
    $expiresIn = $newAccessToken['expires_in'];

    // Update the database with the new access token
    $stmt = $conn->prepare("UPDATE tokens SET access_token = ?, refresh_token = ?, expires_in = ?, created_at = NOW() WHERE service = 'google'");
    $stmt->bind_param("ssi", $access_token, $refreshToken, $expiresIn);
    $stmt->execute();

    // Close the statement
    $stmt->close();
}
