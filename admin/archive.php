<?php
// CORE SETTINGS
require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

if(!is_logged_in()){
    login_error_redirect();
}

include './includes/head.php';
include './includes/navbar.php';

$prodWhereOne = $db->query("SELECT * FROM products WHERE deleted = '1' ");


if(isset($_GET['restore'])){
    $archive_id = (int)$_GET['restore'];
    $archive_id = sanitize($archive_id);
    $db->query("UPDATE products SET deleted = '0' WHERE id = '$archive_id' ");
    header("Location: products.php");
}


?>

<!-- ARCHIVE PRODUCTS -->
<h1 class="text-center text-uppercase">Archived Products</h1>
<hr/>
<div class="p-5">
    <table class="table table-bordered table-striped">
        <thead class="bg-warning">
            <th>Restore</th>
            <th>Products</th>
            <th>Price</th>
            <th>Category</th>
            <th>Sold</th>
        </thead>
        <tbody>
            <?php while($products = mysqli_fetch_assoc($prodWhereOne)):
                
                $childID = $products['categories'];
                $catResult = $db->query("SELECT * FROM categories WHERE id = $childID");
                $childs = mysqli_fetch_assoc($catResult);

                $parentID = $childs['parent'];
                $parentResult = $db->query("SELECT * FROM categories WHERE id = $parentID");
                $parents = mysqli_fetch_assoc($parentResult);

                $finalResult = $parents['category']." : ".$childs['category'];
                ?>
                <tr>
                    <td>
                        <a href="archive.php?restore=<?=$products['id'];?>" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span></a>
                    </td>
                    <td><?=$products['title'];?></td>
                    <td><?=money($products['price']);?></td>
                    <td><?=$finalResult;?></td>
                    <td>0</td>
                </tr>
            <?php endwhile?>
        </tbody>
    </table>
</div>


<?php 
include './includes/footer.php';
?>