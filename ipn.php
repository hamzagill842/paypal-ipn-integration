 <?php

 include_once 'config.php';
//  include_once 'path-to-your-email-config-file.php';
 /*
* Read POST data
* reading posted data directly from $_POST causes serialization
* issues with array data in POST.
* Reading raw POST data from input stream instead.
*/
 $raw_post_data = file_get_contents('php://input');
 $raw_post_array = explode('&', $raw_post_data);
 $myPost = array();
 foreach ($raw_post_array as $keyval) {
     $keyval = explode ('=', $keyval);
     if (count($keyval) == 2)
         $myPost[$keyval[0]] = urldecode($keyval[1]);
}
 // Read the post from PayPal system and add 'cmd'
 $req = 'cmd=_notify-validate';
{
     $get_magic_quotes_exists = true;
}
 foreach ($myPost as $key => $value) {
    $value = urlencode($value);
    $req .= "&$key=$value";
}
 /*
* Post IPN data back to PayPal to validate the IPN data is genuine
* Without this step, anyone can fake IPN data
*/
 $paypalURL = PAYPAL_URL;

 $ch = curl_init($paypalURL);
 if ($ch == FALSE) {
     echo  'test';
}
 curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
 curl_setopt($ch, CURLOPT_POST, 1);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
 curl_setopt($ch, CURLOPT_SSLVERSION, 6);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
 curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
 // Set TCP timeout to 30 seconds
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: company-name'));
 $res = curl_exec($ch);
 /*
* Inspect IPN validation result and act accordingly
* Split response headers and payload, a better way for strcmp
*/
 $tokens = explode("\r\n\r\n", trim($res));
 $res = trim(end($tokens));

 if (strcmp($res, "VERIFIED") == 0 || strcasecmp($res, "VERIFIED") == 0) {

     // Retrieve transaction info from PayPal

     $transactionID  = $_POST['txn_id'];
     $created        = $_POST['payment_date'];
     $payer_first_name    = $_POST['first_name'];
     $payer_last_name  = $_POST['last_name'];
     $payer_email    = $_POST['payer_email'];
     $currency_code  = $_POST['mc_currency'];
     $address_street = isset($_POST['address_street']) ? $_POST['address_street'] : '' ;
     $address_city = isset($_POST['address_city']) ? $_POST['address_city'] : '';
     $address_state = isset($_POST['address_state']) ? $_POST['address_state'] : '' ;
     $address_zip = isset($_POST['address_zip']) ? $_POST['address_zip'] : '';
     $time_stamp = $_POST['payment_date'];
     $address_country = isset($_POST['address_country_code']) ? $_POST['address_country_code'] : (isset($_POST['residence_country']) ? $_POST['residence_country'] : '');

     $payment_status = $_POST['payment_status'];
     $shipping = (isset($_POST['mc_shipping']) and $_POST['mc_shipping'] != 0 ) ? $_POST['mc_shipping'] : '' ;
     $dateFormat = DateTime::createFromFormat("H:i:s M d, Y T", $_POST['payment_date']);

     $time = $dateFormat->format("H:i:s");
     $date = $dateFormat->format("d.m.Y");
     $year = $dateFormat->format("Y");
     $cartItems = [];

     $txn_type  = isset($_POST['txn_type']) ? $_POST['txn_type'] : 'refunded';
     if ($txn_type == 'send_money') {

         $cartItems[0] = handleCartItems(1,$_POST['memo'],(float)$_POST['mc_gross'],'');

     } elseif($txn_type == 'invoice_payment' || $txn_type == 'cart') {

         $total_item = $_POST['num_cart_items'];

         for ($i = 0; $i < $total_item; $i++) {

             $cartItems[$i] = handleCartItems(
                 $_POST['quantity'.($i+1)],
                 $_POST['item_name'.($i+1)],
                 ($_POST['mc_gross_'.($i+1)]/$_POST['quantity'.($i+1)]),
                 ''
             );
         }

         if ($_POST['discount'] > 0) {
             $discount = (float) $_POST['discount'];
             $discount = number_format($discount, 2, '.', '');
             $cartItems[$total_item] = handleCartItems(1, 'Discount','-'. $discount,'');
         }

     } elseif ($txn_type == 'refunded' || $payment_status == 'Refunded') {
         $total_item = getMaxQuantityNumber($_POST);
         $product_name            = findRefundedProduct($total_item);

             $cartItems[0] = handleCartItems(
                 1,
                 $product_name,
                 $_POST['mc_gross'],
                 $_POST['parent_txn_id']
             );
     } else {
         $cartItems[0] = handleCartItems(
             isset($_POST['quantity']) ? $_POST['quantity'] : $_POST['num_cart_items'],
             isset($_POST['item_name']) ? $_POST['item_name'] : $_POST['item_name1'],
             (float)$_POST['mc_gross'],''
         );

         if ($_POST['discount'] > 0) {
             $discount = (float) $_POST['discount'];
             $discount = number_format($discount, 2, '.', '');
             $cartItems[1] = handleCartItems(1, 'Discount', '-'.$discount,'');
         }
     }

     include ('googleSheet.php');

} else{
     echo  'here';
 }

 function handleCartItems($quantity,$name, $amount, $parent_id = '')
 {
     return [
         'quantity' => $quantity,
         'name' => $name,
         'amount' => $amount,
         'parent_txn_id' => $parent_id
     ];
 }


 function getMaxQuantityNumber($formData) {
     $maxQuantity = 0;

     foreach ($formData as $key => $value) {
         if (preg_match('/^quantity(\d+)$/', $key, $matches) && is_numeric($value)) {
             $numValue = (int)$matches[1];
             $maxQuantity = max($maxQuantity, $numValue);
         }
     }

     return $maxQuantity;
 }


 function findRefundedProduct($qty) {
     // Find refunded product based on quantity
     $refunded_product = '';
     $actual_refunded_amount = $_POST['mc_gross'];
     for ($i = 0; $i < $qty; $i++) {
         $total_cost = ($_POST['mc_gross_'.$i+1] - $actual_refunded_amount) * $_POST['quantity'.$i+1];

         if ($total_cost < 0) {
             $refunded_product = $_POST['item_name'.$i+1];
             break;
         }
     }

     if (empty($refunded_product)) {
         $refunded_product = $_POST['item_name1'];
     }

     return $refunded_product;
 }

 ?>