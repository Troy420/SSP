<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

$id = $_POST['id'];
$id = (int)$id;

// PRODUCT QUERY
$sql = "SELECT * FROM products WHERE id = '$id' ";
$result = $db->query($sql);
$product = mysqli_fetch_assoc($result);

// BRAND QUERY
$brand_id = $product['brand'];
$sql_brand = "SELECT brand FROM brand WHERE id = '$brand_id' ";
$brand_query = $db->query($sql_brand);
$brand = mysqli_fetch_assoc($brand_query);

// SIZE QUERY
$size_string = $product['sizes'];
$size_array = explode(',', $size_string);
?>

<?php ob_start(); ?>

<div class="detail-wrapper" id="<?= $product['id'] ?>">
    
    <div class="all-wrap">
        <span class="close-btn" onclick="closemodal()">X</span>
        <div class="img-box">
            <img src="<?= $product['image'] ?>" alt="<?= $product['title']; ?>">
        </div>
        <div class="detail-box">            
            <span id="modal_errors"></span>
            <p class="prod-brand">Brand: <?= $brand['brand']; ?></p>
            
            <!-- FORMS -->
            <form action="add_cart.php" method="post" id="add_product_form">
                <input type="hidden" name="product-isset" value="<?= $id; ?>">
                <input type="hidden" name="available-isset" id="available-id" value="">
                <h3 class="title"><?= $product['title']; ?></h3>
                <p class="price">$ <?= $product['list_price']; ?></p>
                <p class="desc"><?= $product['description']; ?></p>
                <hr>
                <div>
                    <label>Size: </label>
                    <select name="size-isset" id="size-id" class="custom-width">
                        <option value=""></option>
                        <?php foreach($size_array as $string){
                            $string_array = explode(' : ', $string);
                            $size = $string_array[0];
                            $available_size = $string_array[1];
                            echo '<option value="' .$size. '"data-available="'.$available_size.'">
                                '.$size.' ('.$available_size.' Available)
                            </option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label>Qty: </label>
                    <input type="number" min="0" name="quantity-isset" id="quantity-id" class="custom-width">
                </div>
                <div>
                        <input type="button" onclick='add_to_cart(); return false' value="add to cart" class='primary-btn text-center'>
                </div>
            </form>
            <!-- <button class="primary-btn text-uppercase" onclick="add_to_cart(); return false;" >add to cart</button> -->
        </div>
    </div>
</div>



<?= ob_get_clean(); ?>
<script>
// function to change the available size
jQuery("#size-id").change(function() {
  var new_available = jQuery("#size-id option:selected").data("available");
  jQuery("#available-id").val(new_available);
});
</script>