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
                        $selectedProductsTable = $db->query("SELECT * FROM `products` WHERE `id` = '$product_id' ");
                        $allProductsData = mysqli_fetch_assoc($selectedProductsTable);

                        $sArray = explode(',',$allProductsData['sizes']);
                        // THE EXPLODED ARRAY WILL LOOK LIKE THIS: Small : 3 Medium : 2
                        foreach($sArray as $sizeString){
                            // SO WE NEED TO EXPLODE THEM AGAIN INTO ARRAYS
                            $s = explode(' : ',$sizeString);
                        
                            // THE EXPLODED ARRAY WILL LOOK LIKE THIS: [0]Small [1]3, [0]Medium [1]2
                            if($s[0] == $items['size']){
                                $available = $s[1];
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
                                <?php if($items['quantity'] < $available): ?>
                                    <button class="btn btn-sm btn-success" onclick="update_cart('plusone', '<?=$items['id'];?>', '<?=$items['size'];?>');">+</button>
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
    <div class="modal fade" id="checkoutModal" tabindex="-10" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Shipping Address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="thank-you.php" method="post" id="payment-form">
                        <p class="bg-danger text-white text-center" id="payment-error"></p>
                        <div id="step1">
                            <div class="form-group col-md-6">
                                <label for="full_name">Full Name :</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" data-stripe="name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Email :</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="street">Street Address :</label>
                                <input type="text" class="form-control" id="street" name="street" data-stripe="address_line1">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="street2">Street Address 2 :</label>
                                <input type="text" class="form-control" id="street2" name="street2" data-stripe="address_line2">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="city">City :</label>
                                <input type="text" class="form-control" id="city" name="city" data-stripe="address_city">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="zip-code">Zip Code :</label>
                                <input type="text" class="form-control" id="zip-code" name="zip-code" data-stripe="address_zip">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phone-no">Phone No :</label>
                                <input type="text" class="form-control" id="phone-no" name="phone-no">
                            </div>
                        </div>  
                        <div id="step2">
                            <div class="form-group col-md-4">
                            <!-- FOR SECURITY REASON, DO NOT USE name attribute, because name attribute passes the information to the server and we do not want to share our credit card information to anyone -->
                                <label for="name">Name on Card:</label>
                                <input type="text" id="name" class="form-control" data-stripe="name">
                            </div>
                            <div class = "form-group col-md-4">
                                <label for="cc-no">Card Number:</label>
                                <input type="text" id="cc-no" class="form-control" data-stripe="number">
                            </div>
                            <div class = "form-group col-md-2">
                                <label for="cvc">CVC:</label>
                                <input type="text" id="cvc" class="form-control" data-stripe="cvc">
                            </div>
                            <div class = "form-group col-md-3">
                                <label for="exp-month">Expire Month:</label>
                                <select id="exp-month" class="form-control" data-stripe="exp_month">
                                    <option value=""></option>
                                    <?php for($i=1; $i<13; $i++): ?>
                                        <option value="<?= $i; ?>"><?= $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class = "form-group col-md-3">
                                <label for="exp-year">Expire Year:</label>
                                <select id="exp-year" class="form-control" data-stripe="exp_year">
                                    <option value=""></option>
                                    <?php $year = date("Y"); ?>
                                    <?php for($i = 0; $i < 11; $i++ ): ?>
                                        <option value="<?=$year + $i ?>"><?= $year + $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="check_address();" id="next-btn">Next</button>

                    <button type="button" class="btn btn-warning" onclick="back_address();" id="back-btn">Back</button>
                    <button type="submit" class="btn btn-success" id="check-out-btn">Check Out</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class='fixed-bottom text-center p-3'>
    &copy; Copyright 2019 Simplicity
</div>

<script>
    function back_address(){
        jQuery('#payment-error').html("");
        jQuery('#step1').css('display','flex');
        jQuery('#step2').css('display','none');
        jQuery('#next-btn').css('display','inline-block');
        jQuery('#back-btn').css('display','none');
        jQuery('#check-out-btn').css('display','none');
        jQuery('#checkoutModalLabel').html("Shipping Address");
    }

    function check_address(){
        var data = {
            'full_name' : jQuery('#full_name').val(),
            'email' : jQuery('#email').val(),
            'street' : jQuery('#street').val(),
            'street2' : jQuery('#street2').val(),
            'city' : jQuery('#city').val(),
            'zip-code' : jQuery('#zip-code').val(),
            'phone-no' : jQuery('#phone-no').val(),
        };

        jQuery.ajax({
            url : '/ssp/admin/parser/check_address.php',
            method : 'post',
            data: data,
            // so the data inside the function does not come from the above data, instead it comes from the url, which is check_addres.php
            success : function(data){
                if(data != 'passed'){
                    jQuery('#payment-error').html(data);
                }
                if(data == 'passed'){
                    jQuery('#payment-error').html("");
                    jQuery('#step1').css('display','none');
                    jQuery('#step2').css('display','flex');
                    jQuery('#next-btn').css('display','none');
                    jQuery('#back-btn').css('display','inline-block');
                    jQuery('#check-out-btn').css('display','inline-block');
                    jQuery('#checkoutModalLabel').html("Enter Your Card Details");
                }
            },
            error : function(){
                alert("Something is wrong in check_address function");
            }
        });
    }

    Stripe.setPublishableKey('<?= STRIPE_PUBLISHABLE ?>');

    Stripe.card.createToken({
        'number': $('#cc-no').val(),
        'cvc': $('#cvc').val(),
        'exp_month': $('#exp-month').val(),
        'exp_year': $('#exp-year').val()
    }, stripeResponseHandler);

    function stripeResponseHandler(status, response) {

    // Grab the form:
    var $form = $('#payment-form');

    if (response.error) { // Problem!

    // Show the errors on the form
    $form.find('#payment-error').text(response.error.message);
    $form.find('button').prop('disabled', false); // Re-enable submission

    } else { // Token was created!

    // Get the token ID:
    var token = response.id;

    // Insert the token into the form so it gets submitted to the server:
    $form.append($('<input type="hidden" name="stripeToken" />').val(token));

    // Submit the form:
    $form.get(0).submit();

    }
}
</script>

<?php
include './includes/script.php';
?>