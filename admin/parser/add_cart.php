<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

$product_id = isset($_POST['product-isset']) ? sanitize($_POST['product-isset']) : '';
$size = isset($_POST['size-isset']) ? sanitize($_POST['size-isset']) : '';
$available = isset($_POST['available-isset']) ? sanitize($_POST['available-isset']) : '';
$quantity = isset($_POST['quantity-isset']) ? sanitize($_POST['quantity-isset']) : '';
// $item = array();
$item[] = array(
    'id' => $product_id,
    'size' => $size,
    'quantity' => $quantity,
);

$domain = ( ($_SERVER['HTTP_HOST'] != 'localhost')? '.'.$_SERVER['HTTP_HOST'] : false );

$selectedProductsTable = $db->query("SELECT * FROM `products` WHERE `id` = '{$product_id}' ");
$allProductsData = mysqli_fetch_assoc($selectedProductsTable);
$_SESSION['success_flash'] = $allProductsData['title']. ' has been added to your cart.';

// Check to see if the cart cookie exists 
if($cart_id != ""){
    $cartQ = $db->query("SELECT * FROM `cart` WHERE `id` = '{$cart_id}'");
    $cart = mysqli_fetch_assoc($cartQ); 

    // first we have to make a duplicate copy of the original 
    $previous_items = json_decode($cart['items'], true);

    // Initialize $item_match to check what we are adding matches any of the items in the database
    $item_match = 0;
    $new_items = array();
    foreach($previous_items as $pitem){
        if($item[0]['id'] == $pitem['id'] && $item[0]['size'] == $pitem['size']){
            $pitem['quantity'] = $pitem['quantity'] + $item[0]['quantity'];
            if($pitem['quantity'] > $available){
                $pitem['quantity'] = $available;
            }
            $item_match = 1;
        }
        $new_items[] = $pitem;
    }
    if($item_match != 1 ) {
        $new_items = array_merge($item,$previous_items);
    }

    $items_json = json_encode($new_items);
    $cart_expire = date("Y-m-d H:i:s", strtotime("+31 days"));
    $db->query("UPDATE `cart` SET `items` = '$items_json', `expiry_date` = '$cart_expire' WHERE `id` = '$cart_id' ");
    
    // Reset cookie time
    setcookie(CART_COOKIE, '', 1, "/", $domain, false);

    // Set cookie time to a month
    setcookie(CART_COOKIE, $cart_id, CART_COOKIE_EXPIRE, '/', $domain, false);

} else {

    // add the cart to the database and set cookie
    $items_json = json_encode($item);
    $cart_expire = date('Y-m-d H:i:s', strtotime("+31 days"));

    $db->query("INSERT INTO `cart` (`items`, `expiry_date`) VALUES ('$items_json', '$cart_expire')");

    // grabbing the last item's updated id and storing it
    $cart_id = $db->insert_id;

    // setcookie(name, value, expire, path, domain, secure, httponly);
    setcookie(CART_COOKIE, $cart_id, CART_COOKIE_EXPIRE, '/', null);
}

?>