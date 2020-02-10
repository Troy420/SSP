<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

$name = sanitize($_POST['full_name']);
$email = sanitize($_POST['email']);
$street = sanitize($_POST['street']);
$street2 = sanitize($_POST['street2']);
$city = sanitize($_POST['city']);
$zip_code = sanitize($_POST['zip-code']);
$phone_no = sanitize($_POST['phone-no']);
$subtotal = sanitize($_POST['subtotal']);
$grandtotal = sanitize(intval($_POST['grandtotal']));
$cart_id = sanitize($_POST['cart_id']);
$description = sanitize($_POST['description']);
$charge_amount = number_format($grandtotal, 2) * 100;
$metadata = array(
  "cart_id" => $cart_id,
  "tax" => $tax,
  "subtotal" => $subtotal,
);



// Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey(STRIPE_SECRET);

$payment_intent = \Stripe\PaymentIntent::create([
  'payment_method_types' => ['card'],
  'amount' => 1000,
  'currency' => 'usd',
], ['stripe_account' => '{{CONNECTED_STRIPE_ACCOUNT_ID}}']);

?>