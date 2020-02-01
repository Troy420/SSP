<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

    if(!is_logged_in()){
        login_error_redirect();
    }

    include './includes/head.php';
    include './includes/navbar.php';

    // get brand details from database
    $sql = "SELECT * FROM brand ORDER BY brand";
    $results = $db->query($sql);
    $errors = array();

    // Delete Brand
    if( isset($_GET['delete']) && !empty($_GET['delete']) ){
        $delete_id = (int)$_GET['delete'];
        $delete_id = sanitize($delete_id);
        $sql = "DELETE FROM brand WHERE id = '$delete_id' ";
        $db->query($sql);
        header('Location: brand.php');
    }

    // Edit Brand
    if( isset($_GET['edit']) && !empty($_GET['edit']) ){
        $edit_id = (int)$_GET['edit'];
        $edit_id = sanitize($edit_id);
        $sql2 = "SELECT * FROM brand WHERE id = '$edit_id' ";
        $edit_result = $db->query($sql2);
        $brandData = mysqli_fetch_assoc($edit_result);
    }

    // conditions for form submission
    if(isset($_POST['add_submit'])){
        $brand = sanitize(mysqli_real_escape_string($db, $_POST['brand']));
        // check if brand is blank
        if($_POST['brand'] == ""){
            $errors[] .= "You must enter a brand!";
        }
        // check if brand already exist in the database
        $sql = "SELECT * FROM brand WHERE brand = '$brand' ";
        if(isset($_GET['edit'])){
            $sql = "SELECT * FROM brand WHERE brand = '$brand' AND id != '$edit_id' ";
        }
        $result = $db->query($sql);
        $count = mysqli_num_rows($result);
        if($count != 0){
            $errors[] .= $brand.' already exists';
        }

        // display errors
        if(!empty($errors)){
            echo display_errors($errors);
        }else{
            // add brand to database
            $sql = "INSERT INTO brand (brand) VALUES ('$brand')";
            if(isset($_GET['edit'])){
                $sql = "UPDATE brand SET brand = '$brand' WHERE id = '$edit_id' ";
            }
            $db->query($sql);
            header('location: brand.php');
        }
    };

?>

<h1 class="text-center mt-4 text-uppercase">brands</h1><hr/>


<!-- FORM -->
<div class="d-flex justify-content-center">
    <form class="form-inline" action="brand.php<?=(isset($_GET['edit']) ? '?edit='.$edit_id : '' ); ?>" method="post">
        <div class="form-group">


            <?php 


                $brand_value = "";

                if(isset($_GET['edit'])){
                    $brand_value = $brandData['brand'];
                } else {
                    if(isset($_POST['brand'])){
                        $brand_value = sanitize($_POST['brand']);
                    }
                    
                }
            ?>

            <label for="brand"><?=( (isset($_GET['edit'])) ? "Edit" : "Add a" ); ?> Brand: </label>
            <input type="text" name="brand" id="brand" class="form-control mx-3" value="<?= $brand_value; ?>">

            <!-- CANCEL BUTTON -->
            <?php if(isset($_GET['edit'])): ?>
                <a href="brand.php" class="btn btn-secondary mr-3">Cancel</a>
            <?php endif; ?>

            <input type="submit" name="add_submit" value="CONFIRM" class="btn btn-success">
        </div>
    </form>
</div>
<hr/>
<table class="table table-bordered table-striped table-auto">
    <thead class="bg-warning">
        <!-- <th>Edit</th> -->
        <th>Brand</th>
        <th>Edit / Delete</th>
    </thead>
    <tbody>
        <?php while($brand = mysqli_fetch_assoc($results)) : ?>
        <tr>
            <!-- <td></td> -->
            <td><?= $brand['brand'] ?></td>
            <td class="text-center">
                <a href="brand.php?edit=<?= $brand['id']?>" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="brand.php?delete=<?= $brand['id']?>" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<hr/>
<?php 
    include './includes/footer.php';
?>