<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

// ------------------------------------------------------------------------------------------------------------- HEAD
include './includes/head.php'; 
include './includes/navbar.php';
// END HEAD


if(isset($_GET['category'])){
    $cat_id = (int)$_GET['category'];
}else{
    $cat_id = "";
}

$sql2 = "SELECT * FROM `products` WHERE `categories` = '$cat_id' AND `deleted` = '0' ";
$catQ = $db->query($sql2); 
$category = get_categories($cat_id);



// ------------------------------------------------------------------------------------------------------------- NAVBAR
// $sql = "SELECT * FROM categories WHERE parent = 0";
// $pquery = $db->query($sql);
?>

<!--------------------------------------------------------------------------------------------------------------- MAIN CONTENT -->

<section id="featured">
    <div class="col-md-8 mx-auto">
    <h2 class="text-center"><?= $category['parent'] . "  " . $category['child'] ?></h2>
        <?php 
            if(isset($_SESSION['success_flash'])){
                echo '<div class="bg-success"><p class="text-white text-center">'.$_SESSION['success_flash'].'</p></div>';
                unset($_SESSION['success_flash']);
            }
        ?>
        <div class="row">
            <!-- Return array -->
            <?php while($product = mysqli_fetch_assoc($catQ)) : ?> 
                <div class="col-sm-3 text-center">
                    <h4><?= $product['title']; ?></h4>
                    <img src="<?= $product['image']; ?>" alt="<?php echo $product['title']; ?>" class="img-thumbnail">
                    <p class="list price text-danger">List Price: <s>$<?= $product['list_price']; ?></s></p>
                    <p class="price">Our Price: $<?= $product['price']; ?></p>
                    <button type="button" class="primary-btn details" onclick="detailsmodal(<?= $product['id'];  ?>)">Details</button>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<!-- END OF MAIN CONTENT -->

<?php
// -------------------------------------------------------------------------------------------------------------------------------------- FOOTER
include './includes/footer.php';
// END FOOTER

// --------------------------------------------------------------------------------------------------------------------------------------- SCRIPT
?>
<!-- SCRIPTS -->
<script src="./js/jquery.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/10c3c5053e.js"></script>
<script src="./js/custom.js"></script>
</body>
</html>



