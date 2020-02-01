<?php
// CORE SETTINGS
require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

if(!is_logged_in()){
    login_error_redirect();
}

include './includes/head.php';
include './includes/navbar.php';

    // MOVE ITEM TO ARCHIVE
     if(isset($_GET['delete'])){
        $delete_id = sanitize($_GET['delete']);
        $db->query("UPDATE products SET deleted = '1' WHERE id = $delete_id ");
        header('Location: products.php');
    }

$dbPath = "";

// INITIALIZING VARIABLES ON BOTH ADD AND EDIT PAGES
if(isset($_GET['add']) || isset($_GET['edit'])){
$brandQuery = $db->query("SELECT * FROM brand ORDER BY brand");
$parentQuery = $db->query("SELECT * FROM categories WHERE parent  = 0 ORDER BY category");


$title = ( (isset($_POST['title_isset']) && !empty($_POST['title_isset']) ) ?  sanitize($_POST['title_isset']) : "" );
$brand = ( (isset($_POST['brand_isset']) && !empty($_POST['brand_isset']) ) ?  sanitize($_POST['brand_isset']) : "" );
$parent = ( (isset($_POST['parent_isset']) && !empty($_POST['parent_isset']) ) ?  sanitize($_POST['parent_isset']) : "" );
$categories = ( (isset($_POST['child_isset']) && !empty($_POST['child_isset']) ) ?  sanitize($_POST['child_isset']) : "" );
$price = ( (isset($_POST['price_isset']) && !empty($_POST['price_isset']) ) ?  sanitize($_POST['price_isset']) : "" );
$list_price = ( (isset($_POST['list_price_isset']) && !empty($_POST['list_price_isset']) ) ?  sanitize($_POST['list_price_isset']) : "" );
$description = ( (isset($_POST['description_isset']) && !empty($_POST['description_isset']) ) ?  sanitize($_POST['description_isset']) : "" );
$sizes = ( (isset($_POST['sizes_isset']) && !empty($_POST['sizes_isset']) ) ?  sanitize($_POST['sizes_isset']) : "" );
$saved_images = "";


    // EDIT MODE
    if(isset($_GET['edit'])){
        // EDIT QUERY
        $edit_id = (int)$_GET['edit'];
        $editQuery = $db->query("SELECT * FROM products WHERE id = '$edit_id' ");
        $edit = mysqli_fetch_assoc($editQuery);

        // DELETE IMAGE UNDER EDIT MODE
        if(isset($_GET['delete_image'])){
            $image_url = $_SERVER['DOCUMENT_ROOT'].$edit['image']; 
            unlink($image_url);
            $db->query("UPDATE products SET image = '' WHERE id = '$edit_id' ");
            header('Location: products.php?edit='.$edit_id);
        }

        // EDIT TITLE
        $title = ( (isset($_POST['title_isset']) && !empty($_POST['title_isset']) ) ?  sanitize($_POST['title_isset']) : $edit['title']);

        // EDIT BRAND
        $brand = ( (isset($_POST['brand_isset']) && !empty($_POST['brand_isset']) ) ?  sanitize($_POST['brand_isset']) : $edit['brand'] );

        // EDIT CHILD CATEGORY ID
        $categories = ( (isset($_POST['child_isset']) && !empty($_POST['child_isset']) ) ?  sanitize($_POST['child_isset']) : $edit['categories'] );
  
        // EDIT PARENT CATEGORY
        $parentQ = $db->query("SELECT * FROM categories WHERE id = '$categories'");
        $parentR = mysqli_fetch_assoc($parentQ);
        $parent = ( (isset($_POST['parent_isset']) && !empty($_POST['parent_isset']) ) ?  sanitize($_POST['parent_isset']) : $parentR['parent'] );

        // EDIT PRICE CATEGORY
        $price = ( (isset($_POST['price_isset']) && !empty($_POST['price_isset']) ) ?  sanitize($_POST['price_isset']) : $edit['price'] );
        
        // EDIT LIST PRICE CATEGORY
        $list_price = ( (isset($_POST['list_price_isset']) && !empty($_POST['list_price_isset']) ) ?  sanitize($_POST['list_price_isset']) : $edit['list_price'] );

        // EDIT DESCRIPTION
        $description = ( (isset($_POST['description_isset']) && !empty($_POST['description_isset']) ) ?  sanitize($_POST['description_isset']) : $edit['description'] );

        // EDIT SIZES
        $sizes = ( (isset($_POST['sizes_isset']) && !empty($_POST['sizes_isset']) ) ?  sanitize($_POST['sizes_isset']) : $edit['sizes'] );
        
        // EDIT IMAGES
        $saved_images = ($edit['image'] != "")? $edit['image'] : "";
        $dbPath = $saved_images;

        // SEPARATING THE SIZE FORMAT INTO ARRAYS
        if(!empty($sizes)){
            $sizeString = $sizes;
            $sizeArray = explode(" , ", $sizeString);
            $sArray = array();
            $qArray = array();
            foreach($sizeArray as $ss){
                $s = explode(' : ', $ss);
                $sArray[] = $s[0];
                $qArray[] = $s[1];
            }
        }else{
            $sizeArray = array();
        }
    }

    if($_POST){
        $errors = array();

        $required = array('title_isset', 'brand_isset', 'price_isset', 'parent_isset', 'child_isset', 'sizes_isset');
        foreach($required as $field){
            if($_POST[$field] == ""){
                $errors[] = "All fields with an Asterisk are required";
                break;
            } else{
                "";
            }
        }

        if(!empty($_FILES)){
            // var_dump($_FILES);
            $photo = $_FILES['photo_isset']; 
            $name = $photo['name'];
            $nameArray = explode('.', $name);
            $fileName = $nameArray[0];
            $fileExt = $nameArray[1];
            $tmpLoc = $photo['tmp_name'];
            $fileSize = $photo['size'];
            $allowed = array('png', 'jpg', 'jpeg', 'gif');
            $uploadName = md5(microtime()).".".$fileExt;
            $uploadPath = $_SERVER['DOCUMENT_ROOT'].'/ssp/img/products/'.$uploadName;
            $dbPath = '/ssp/img/products/'.$uploadName;

            if(!in_array($fileExt, $allowed)){
                $errors[] = "The photo extension must be in png, jpg, jpeg, or gif";
            }else{
                "";
            }
        
            if($fileSize > 2000000){
                $errors[] = "The file size must be under 2mb";
            }else{
                "";
            }
        }


        if(!empty($errors)){
            echo display_errors($errors);
        }else{
            // UPLOAD FILE AND INSERT INTO DATABASE
            if(!empty($_FILES)){
                move_uploaded_file($tmpLoc, $uploadPath);
            }
            $insertSql = "INSERT INTO products (`title`, `price`, `list_price`, `brand`, `categories`, `sizes`, `image`, `description`) VALUES ('$title', '$price', '$list_price', '$brand', '$categories', '$sizes', '$dbPath', '$description')";

            // EDIT DATABASE
            if(isset($_GET['edit'])){
                $insertSql = "UPDATE products SET `title` = '$title', `brand` = '$brand', `price` = '$price', `list_price` = '$list_price', `categories` = '$categories',  `description` =  '$description', `sizes` = '$sizes', `image` = '$dbPath' WHERE id = '$edit_id'";
            }

            $db->query($insertSql);
            header('Location: products.php');
        }
    }
?>

    <h1 class="text-center"><?=((isset($_GET['edit']))?"Edit " : "Add");?> Product</h1>
    <hr/>
    <!-- FORM -->
    <form action="products.php?<?=((isset($_GET['edit']))? 'edit='.$edit_id : "add=1");?>" method="POST" enctype="multipart/form-data" class="d-flex flex-wrap">
        <div class="form-group col-md-3">
            <label for="title_for">Title <span style="color:red">*</span></label>
            <input type="text" class="form-control" name="title_isset" id="title_for" value="<?= $title ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="brand_for">Brand <span style="color:red">*</span></label>
            <select name="brand_isset" id="brand_for" class="form-control">
                <option value="" <?=($brand == "") ? " selected" : " ";?>></option>
                <?php while($b = mysqli_fetch_assoc($brandQuery)) :?>
                    <option value="<?= $b['id']; ?>" <?=(($brand == $b['id']) ? " selected" : " ");?>><?=$b['brand']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="parent_for">Parent Category <span style="color: red">*</span></label>
            <select name="parent_isset" id="parent_for" class="form-control">
                <option value="" <?=(($parent == "") ? " selected" : " ");?>></option>
                <?php while($p = mysqli_fetch_assoc($parentQuery)) :?>
                    <option value="<?=$p['id'];?>" <?=(($parent == $p['id']) ? " selected" : " ");?> ><?=$p['category'];?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="child_for">Child Category <span style="color:red">*</span></label>
            <select name="child_isset" id="child_for" class="form-control">
                <option value="<?=$edit['categories']?>">
                    <?=((isset($_GET['edit']))? $parentR['category'] : "");?>
                </option>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="price_for">Price <span style="color:red">*</span></label>
            <input type="text" id="price_for" name="price_isset" class="form-control" value=" <?= $price ?> ">
        </div>
        <div class="form-group col-md-3">
            <label for="price_for">List Price</label>
            <input type="text" id="list_price_for" name="list_price_isset" class="form-control" value="<?= $list_price ?>">
        </div>
        <div class="form-group col-md-3">
            <label>Quantity & Size <span style="color:red">*</span></label>
            <button class="btn btn-primary form-control" onclick="jQuery('#sizeModal').modal('toggle'); return false;">Quantity & Size</button>
        </div>
        <div class="form-group col-md-3">
            <label for="qty_size">Quantity & Size Preview </label>
        <input type="text" name="sizes_isset" id="qty_size" class="form-control" value="<?= $sizes ?>" readonly>
        </div>
        <div class="form-group col-md-6">
            <label for="photo_for">Product Photo: </label>
            <?php if($saved_images != ""): ?>
                <div class="saved-image text-center">
                    <img src=" <?= $saved_images ?> " />
                    <a href="products.php?delete_image=1&edit=<?=$edit_id?>" class="btn btn-danger col-md-6">Delete Image</a>
                </div>
            <?php else: ?>
                <input type="file" name="photo_isset" id="photo_for" class="form-control">
            <?php endif ?>
        </div>
        <div class="form-group col-md-6">
            <label for="description_for">Description: </label>
        <textarea name="description_isset" id="description_for" class="form-control" rows="17"><?=  $description ?></textarea>
        </div>
        <div class="form-group col d-flex justify-content-end">
            <div class="form-group col-md-3">
                <a href="products.php" class="form-control btn btn-secondary">Cancel</a>
            </div>
            <div class="form-group col-md-3">
                <input type="submit" value="<?=((isset($_GET['edit']))?"Edit " : "Add");?> Product" class="form-control btn btn-success">
            </div>
        </div>
    </form>
    <!-- END OF FORM -->

    <!-- MODAL FOR SIZES AND QUANTITY -->
    <div class="modal fade bd-example-modal-lg" id="sizeModal" tabindex="-1" role="dialog" aria-labelledby="sizeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="sizeModalLabel">Quantity & Size</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body d-flex flex-wrap">
            <?php for($i = 1; $i <= 5; $i++): ?>
                <div class="form-group col-md-4">
                    <label for="size<?=$i;?>">Size:</label>
                    <input type="text" name="size<?=$i;?>" id="size<?=$i;?>" value="<?=(!empty($sArray[$i - 1]))?$sArray[$i - 1]:""?>" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label for="qty<?=$i;?>">Quantity:</label>
                    <input type="number" name="qty<?=$i;?>" id="qty<?=$i;?>" value="<?=(!empty($qArray[$i - 1]))?$qArray[$i - 1]:""?>" min="0" class="form-control">
                </div>
            <?php endfor;?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="updateSizes(); jQuery('#sizeModal').modal('toggle'); return false;">Save changes</button>
        </div>
        </div>
    </div>
    </div>


    <?php }else{
    $prodWhereZero = $db->query("SELECT * FROM `products` WHERE `deleted` = '0' ORDER BY `price` DESC");

    if(isset($_GET['featured'])){
        $id = (int)$_GET['id'];
        $featured = (int)$_GET['featured'];
        $featuredSql = "UPDATE products SET featured = '$featured' WHERE id = '$id'";
        $db->query($featuredSql);
        header('Location: products.php');
    }
    ?>

<!-- TABLE FOR PRODUCTS -->
<h1 class="text-center text-uppercase">Products</h1>
<hr/>
<a href="products.php?add=1" class="btn btn-success float-right mx-5 mb-4" id="big-font">Add Product</a>
<div class="p-5">
    <table class="table table-bordered table-striped">
        <thead class="bg-warning">
            <th>Edit / Delete</th>
            <th>Products</th>
            <th>Price</th>
            <th>Category</th>
            <th>Featured</th>
            <th>Sold</th>
        </thead>
        <tbody>
            <?php while($products = mysqli_fetch_assoc($prodWhereZero)) : 
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
                        <a href="products.php?edit=<?=$products['id'];?>" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>
                        <a href="products.php?delete=<?=$products['id'];?>" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
                    </td>
                    <td><?=$products['title'];?></td>
                    <td><?=money($products['price']);?></td>
                    <td><?=$finalResult;?></td>
                    <td>
                        <a href="products.php?featured=<?=(($products['featured'] == 0)? "1" : "0");?>&id=<?=$products['id'];?>" class="btn btn-<?=(($products['featured'] == 1)?"danger":"success");?>">
                            <span class="glyphicon glyphicon-<?=($products['featured'] == 1)?"minus":"plus";?>"></span>
                        </a>
                        <?=($products['featured'] == 1)?"Featured Product":"";?>
                    </td>
                    <td>0</td>
                </tr>
            <?php endwhile;?>
        </tbody>
    </table>
</div>
<?php }
include './includes/footer.php';
?>
