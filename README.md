# PayPal IPN Integration with PHP

## Overview:

This PHP script allows you to integrate PayPal Instant Payment Notification (IPN) into your website. It also logs the transaction details into a Google Sheet and sends email notifications.

## Prerequisites:

Before setting up the integration, ensure you have completed the following steps:

1. **Enable Google Sheet API :**
   - Go to the [Google Cloud Console](https://console.cloud.google.com/).
   - Create a new project or select an existing project.
   - Enable the Google Sheet API  for your project.
   - Create credentials (OAuth client ID) for the application.

2. **Configure API Credentials in Your Project:**
   - Obtain the client ID and client secret from the Google Cloud Console.
   - Add these credentials to your project's configuration file.
   - 

## Installation:

To get started with the project, follow these steps:

1. **Clone the Repository:**
   - Clone this repository to your local machine using the following command:
     ```bash
     git clone https://github.com/your-username/your-repository.git
     ```

2. **Install Composer Dependencies:**
   - Navigate to the project directory:
     ```bash
     cd your-repository
     ```
   - Install Composer dependencies:
     ```bash
     composer install
     ```

3. **Set Up Google Sheet  API Credentials:**
   - Follow the instructions in the "Prerequisites" section to enable and configure the Google Sheet  APIs.

4. **Configure the `config.php` File:**
   - Open the `config.php` file and update the database connection details, Google Calendar and Google Drive API credentials, YouTube API credentials, and any additional configuration details.

    ```php
    <?php
    // config.php

    // Database connection details
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "youtube";

    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);
    global $conn;

    // Google Calendar and Google Drive API credentials
    const GOOGLE_Client_ID = "4560468dfdf07463-jqed90d72efocnd3756q1gtj353temi7.apps.googleusercontent.com";
    const GOOGLE_Client_SERECT = "GOCSddffdPX-EEWkFNX0wBPoboAupucJzL3I8CBQ";
    const GOOGLE_REDIRECT_URI = "https://7f89-2400-adc5-15d-da00-1546-cc9e-7b6e-a58d.ngrok-free.app";
    const GOOGLE_SHEET_ID = "https://7f89-2400-adc5-15d-da00-1546-cc9e-7b6e-a58d.ngrok-free.app";

    // YouTube API credentials
    const PAYPAL_URL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    const PAYPAL_API_URL = "https://api-m.sandbox.paypal.com";
    const PAYPAL_Client_ID = "PAYPAL_Client_ID";
    const PAYPAL_Client_SERECT = "YOUR_PAYPAL_Client_SERECT";

    ?>
    ```

5. **Import Database Schema:**
   - Import the `database.sql` file into your MySQL database after creating it.

## PHP Version Compatibility:

This project requires PHP version 8.1 or higher. Ensure that your development environment is using a compatible PHP version.

Follow these steps to check your PHP version:

```bash
php --version
