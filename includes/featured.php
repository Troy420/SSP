<?php

$sql = "SELECT * FROM products WHERE featured = 1";
$featured = $db->query($sql);
?>

<section id="featured">
    <div class="col-md-8 mx-auto">
    <h2 class="text-center">featured products</h2>
        <?php
            if(isset($_SESSION['success_flash'])){
                echo '<div class="bg-success"><p class="text-white text-center">'.$_SESSION['success_flash'].'</p></div>';
                unset($_SESSION['success_flash']);
            }
        ?>
        <div class="row">

            <!-- Return array -->
            <?php while($product = mysqli_fetch_assoc($featured)) : ?> 
                <div class="col-sm-4 text-center pb-5">
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