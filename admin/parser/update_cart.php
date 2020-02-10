<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

    $mode = (isset($_POST['mode'])) ? sanitize($_POST['mode']) : "";
    $edit_id = (isset($_POST['edit_id'])) ? sanitize($_POST['edit_id']) : "";
    $edit_size = (isset($_POST['edit_size'])) ? sanitize($_POST['edit_size']) : "";

    $selectedCartTable = $db->query("SELECT * FROM `cart` WHERE `id` = '$cart_id'");
    $allCartData = mysqli_fetch_assoc($selectedCartTable);
    $itemsDecoded = json_decode($allCartData['items'], true);
    $updated_items = array();

    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? ".".$_SERVER['HTTP_HOST'] : false;



    // ERROR HERE!
    if($mode == 'minusone'){
        foreach($itemsDecoded as $items){
            if($items['id'] == $edit_id && $items['size'] == $edit_size){
                $items['quantity'] = $items['quantity'] - 1;
            }
            if($items['quantity'] > 0){
                $updated_items[] = $items;
            }
        }
    }

    if($mode == 'plusone'){
        foreach($itemsDecoded as $items){
            if($items['id'] == $edit_id && $items['size'] == $edit_size){
                $items['quantity'] = $items['quantity'] + 1;
            }
            $updated_items[] = $items;
        }
    }

    if(!empty($updated_items)){
        $itemsEncoded = json_encode($updated_items);
        $db->query("UPDATE `cart` SET `items` = '$itemsEncoded' WHERE `id` = '$cart_id' ");
        $_SESSION['UPDATE_SUCCESS'] = 'Your shopping cart has been updated';
    }

    if(empty($updated_items)){
        $db->query("DELETE FROM `cart` WHERE `id` = '$cart_id' ");
        setcookie(CART_COOKIE, "", time() - 3600, "/");
        unset($_COOKIE[CART_COOKIE]);
    }
?>
