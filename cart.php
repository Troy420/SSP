<!-- <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet"> -->
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

include './includes/head.php';
include './includes/navbar.php';

$_SESSION['EMPTY_CART'] = "Your Shopping Cart is Empty!";

if($cart_id != ""){
    $selectedCartTable = $db->query("SELECT * FROM `cart` WHERE `id` = '$cart_id' ");
    $allCartData = mysqli_fetch_assoc($selectedCartTable);

    $dataDecoded = json_decode($allCartData['items'], true); 

    // DATA DECODED WILL LOOK LIKE 
    // array(1) { [0]=> array(3) { ["id"]=> string(2) "23" ["size"]=> string(7) " Medium" ["quantity"]=> string(1) "1" } }
    // $dataDecoded = array(
    //     array(
    //         'id' => $product_id,
    //         'size' => $size,
    //         'quantity' => $quantity,
    //     )
    // );
    $i = 1;
    $subtotal = 0;
    $itemcount = 0;
}
?>

<div class="col-md-10 float-right">
    <h2 class="text-center">My Shopping Cart</h2>
    <hr>
    <?php if($cart_id == ""): ?>
        <div class="bg-danger text-center text-white">
            <?= $_SESSION['EMPTY_CART'] ?>
        </div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <th>#</th>
                <th>ITEMS</th>
                <th>PRICE</th>
                <th>QUANTITY</th>
                <th>SIZE</th>
                <th>SUBTOTAL</th>
            </thead>
            <tbody>
                <?php
                    foreach($dataDecoded as $items){
                        $product_id = $items['id']; 
                        $selectedProductsTable = $db->query("SELECT * FROM `products` WHERE `id` = '$product_id'; ");
                        $allProductsData = mysqli_fetch_assoc($selectedProductsTable);

                        // print_r($allProductsData['sizes']);

                        $explodedArray = explode(",", $allProductsData['sizes']);
                            // print_r($explodedArray);
                            // print_r($explodedArray[1]);
                        // THE EXPLODED ARRAY WILL LOOK LIKE THIS: Small : 3 Medium : 2
                        foreach($explodedArray as $sizeAsString){
                            // SO WE NEED TO EXPLODE THEM AGAIN INTO ARRAYS
                            $explodedSizes = explode(":", $sizeAsString);
                            // print_r($explodedSizes);
                            // print_r($explodedSizes[0]);
                            // print_r($explodedSizes[1]);

                            $sizePicked = $explodedSizes[0];
                            $availableSize = $explodedSizes[1];
                        
                            print_r($items['size']);
                            // print_r($sizePicked);

                            // THE EXPLODED ARRAY WILL LOOK LIKE THIS: [0]Small [1]3, [0]Medium [1]2
                            if($sizePicked == $items['size']){
                                $availableSize = $sizePicked; 
                                // print_r($availableSize);
                            }
                        }
                ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $allProductsData['title'] ?></td>
                            <td><?= money(($allProductsData['price'])) ?></td>
                            <td class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-danger" onclick="update_cart('minusone', '<?=$items['id'];?>', '<?=$items['size'];?>');">-</button>
                                <?= $items['quantity']; ?>
                                <?php if($items['quantity'] < $availableSize): ?>
                                    <button class="btn btn-sm btn-success" onclick="update_cart('plusone', '<?=$items['id'];?>', '<?=$items['size'] + 1;?>');">+</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" readonly>MAX</button>
                                <?php endif; ?>
                            </td>
                            <td><?= $items['size'] ?></td>
                            <td><?= money($allProductsData['price'] * $items['quantity']) ?></td>
                        </tr>
                    <?php
                        $i++;
                        $itemcount += $items['quantity'];
                        $subtotal += $allProductsData['price'] * $items['quantity'];
                    }
                    $tax= TAXRATE * $subtotal;
                    $tax = number_format($tax, 2);
                    $grandtotal = $tax + $subtotal;
                ?>
            </tbody>
        </table>
        <table class="table table-bordered text-right">
            <legend>Totals</legend>
            <thead>
                    <th>Total Items</th>
                    <th>Sub-Total</th>
                    <th>Tax</th>
                    <th>Grand Total</th>
            </thead>
            <tbody>
                <tr>
                    <td><?= $itemcount ?></td>
                    <td><?= money($subtotal) ?></td>
                    <td><?= money($tax) ?></td>
                    <th><?= money($grandtotal) ?></th>
                </tr>
            </tbody>
        </table>
    <?php endif;?>

    <!-- Check Out Button -->
    <button type="button" class="btn btn-check-out float-right" data-toggle="modal" data-target="#checkoutModal">
    <i class="fas fa-cart-arrow-down" aria-hidden="true"></i> Check Out
    </button>

    <!-- Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Shipping Address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class='fixed-bottom text-center p-3'>
    &copy; Copyright 2019 Simplicity
</div>

<?php
include './includes/script.php';
?>