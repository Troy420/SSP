<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

    $mode = (isset($_POST['mode'])) ? sanitize($_POST['mode']) : "";
    $edit_id = (isset($_POST['edit_id'])) ? sanitize($_POST['edit_id']) : "";
    $edit_size = (isset($_POST['edit_size'])) ? sanitize($_POST['edit_size']) : "";

    $selectedCartTable = $db->query("SELECT * FROM `cart` WHERE `id` = '$cart_id'; ");
    $allCartData = mysqli_fetch_assoc($selectedCartTable);
    $ItemsDecoded = json_decode($allCartData['items'], true);
    $updated_items = array();

    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? ".".$_SERVER['HTTP_HOST'] : false;



    // ERROR HERE!
    if($mode == 'minusone'){
        foreach($ItemsDecoded as $items){
            
            $product_id = $items['id']; var_dump($items['id']);
            $selectedProductsTable = $db->query("SELECT * FROM `products` WHERE `id` = '$product_id'; ");
            $allProductsData = mysqli_fetch_assoc($selectedProductsTable);
        }
    }

    if($mode == 'plusone'){
        foreach($ItemsDecoded as $items){
            if($items['id'] == $edit_id && $items['size'] == $edit_size){
                $items['quantity'] = $item['quantity'] + 1;
            }
            $updated_items[] = $Items;
        }
    }

    if(!empty($updated_items)){
        $itemsEncoded = json_encode($updated_items);
        $db->query("UPDATE `cart` SET `items` = '$itemsEncoded' WHERE `id` = '$cart_id' ");
        $_SESSION['UPDATE_SUCCESS'] = 'Your shopping cart has been updated';
    }

    // if(empty($updated_items)){
    //     $db->query("DELETE FROM `cart` WHERE `id` = '$cart_id' ");
    //     setcookie(CART_COOKIE, '', 1, '/', $domain, false);
    // }
?>
