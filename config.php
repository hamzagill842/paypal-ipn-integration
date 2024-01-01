<?php 

//database connect

$host = "localhost";
$username = "root";
$password = "";
$dbname = "demo";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);
global $conn;



const PAYPAL_URL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
const PAYPAL_API_URL = "https://api-m.sandbox.paypal.com";
const PAYPAL_Client_ID = "AeV0OiGotzWpSFe5yJNu8QzOx3kBpKxuMTibDn3m6_ZuOQEqpe6RZU2F-X0r6cVZYS1cxYEJMB3kQJ4M";
const PAYPAL_Client_SERECT = "EEh4xPCzcN8GjCchqZiv2dzkYemyMoGb8ds9AOZpvctyX1JWHQOX8whUn_pVv17uoGo_fbIALgp4OVY3";

const GOOGLE_Client_ID = "806643992958-5cushbi2g79r2r0rvc45ah531os1cf43.apps.googleusercontent.com";
const GOOGLE_Client_SERECT = "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";
const GOOGLE_REDIRECT_URI= "https://41d8-2400-adc5-15d-da00-b15e-a60f-1576-9728.ngrok-free.app";

const GOOGLE_SHEET_ID = '1FWlwDKD3QvMxFArKE0NqR8sIbvq8bD3i0fSTpI-cah4';

?>
