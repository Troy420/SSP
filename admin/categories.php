<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

if(!is_logged_in()){
    login_error_redirect();
}

include 'includes/head.php';
include 'includes/navbar.php';

$sql = "SELECT * FROM categories WHERE parent = 0";
$result = $db->query($sql);
$errors = array();
$category_post = "";
$parent_post = "";

// Delete Category
if(isset($_GET['delete']) && !empty($_GET['delete'])){
    $delete_id = (int)$_GET['delete'];
    $delete_id = sanitize($delete_id);
    $dsql = "DELETE FROM categories WHERE id = $delete_id OR parent = $delete_id";
    $db->query($dsql);
    header('Location: categories.php');
}

// Edit Category
if(isset($_GET['edit']) && !empty($_GET['edit'])){
    $edit_id = (int)$_GET['edit'];
    $edit_id = sanitize($edit_id);
    $esql = "SELECT * FROM categories WHERE id = $edit_id";
    $edit_query = $db->query($esql);
    $edit_category = mysqli_fetch_assoc($edit_query);
}   

$category_value = "";
$parent_value = 0;
if(isset($_GET['edit'])){
    $category_value = $edit_category['category'];
    $parent_value = $edit_category['parent'];
} else {
    if(isset($_POST)){
        $category_value = $category_post;
        $parent_value = $parent_post;
    }
}

// process form
if(isset($_POST) && !empty($_POST)){
    $parent_post = sanitize($_POST['parent']);
    $category_post = sanitize($_POST['category']);
    $sqlform = "SELECT * FROM categories WHERE category = '$category_post' AND parent = '$parent_post' ";
    // if(isset($_GET['edit'])){
    //     $id = $edit_category['id'];
    //     $sqlform = "SELECT * FROM categories WHERE category = '$category_post' AND parent = '$parent_post' AND id != $id";
    // }
    $fresult = $db->query($sqlform);
    $count = mysqli_num_rows($fresult);
    
    // if category is blank
    if($category_post == ""){
        $errors[] .= "The Category cannot be left blank.";
    }

    // if exist in the database
    if($count > 0){
        $errors[] .= $category_post. " already exists. Please choose a new category";
    }

    // Display errors or Update database
    if(!empty($errors)){
        // Display errors
        // $display = display_errors($errors);
        echo display_errors($errors);    
    } else {
        // Update database
        $updatesql = "INSERT INTO categories (category, parent) VALUES ('$category_post', '$parent_post')";
        if(isset($_GET['edit'])){
            $updatesql = "UPDATE categories SET category = '$category_post', parent = '$parent_post' WHERE id = '$edit_id' ";
        }
        $db->query($updatesql);
        header('Location: categories.php');
    }
}
?>

<h1 class="text-center text-uppercase">categories</h1>
<hr/>
<div class="d-flex">
    <!-- FORM -->
    <div class="col-md-6 px-5">
        <form action="categories.php<?= (isset($_GET['edit'])) ? '?edit='.$edit_id : ' '; ?>" method="post">
            <legend class="text-center text-uppercase"><?= (isset($_GET['edit'])) ? 'Edit' : 'Add' ; ?> a Category</legend>
            <div id="error"></div>
            <div class="form-group">
                <label for="parents">Parent</label>
                <select class="form-control" name="parent" id="parent"> 
                    <option value="0"<?=($parent_value == 0) ? 'selected="selected"' : "" ;?>>Parent</option>
                    <?php while($parent = mysqli_fetch_assoc($result)): ?>
                        <option value="<?= $parent['id']; ?>"<?=($parent_value == $parent['id']) ? 'selected="selected"' : "" ;?>><?= $parent['category'];?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" class="form-control" name="category" id="category" value="<?=$category_value; ?>">
            </div>
            <div class="form-group">
                <input type="submit" value="<?=(isset($_GET['edit'])) ? 'Edit' : 'Add'; ?> Category" class='btn btn-success'>
            </div>
        </form>
    </div>

    <!-- CATEGORY TABLE -->
    <div class="col-md-6 px-5">
        <table class="table table-bordered">
            <thead class="bg-warning">
                <th>Category</th>
                <th>Parent</th>
                <th>Edit / Delete</th>
            </thead>
            <tbody>
                <?php 
                    $sql = "SELECT * FROM categories WHERE parent = '0'";
                    $result = $db->query($sql);
                     
                    while($parent = mysqli_fetch_assoc($result)): 
                    $parent_id = (int)$parent['id'];
                    $sql2 = "SELECT * FROM categories WHERE parent = '$parent_id' ";
                    $cresult = $db->query($sql2);
                ?>
                        <tr class="bg-info">
                            <td><?=$parent['category'];?></td>
                            <td>parent</td>
                            <td>
                            <!-- Pencil -->
                                <a href="categories.php?edit=<?=$parent['id']; ?>" class="btn btn-primary"><span class="glypicon glyphicon-pencil"></span></a>
                            <!-- Delete -->
                                <a href="categories.php?delete=<?=$parent['id']; ?>" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
                            </td>
                        </tr>

                        <?php while($child = mysqli_fetch_assoc($cresult)): ?>
                            <tr>
                                <td><?=$child['category'];?></td>
                                <td><?=$parent['category'];?></td>
                                <td>
                                <!-- Pencil -->
                                    <a href="categories.php?edit=<?=$child['id']; ?>" class="btn btn-primary"><span class="glypicon glyphicon-pencil"></span></a>
                                <!-- Delete -->
                                    <a href="categories.php?delete=<?=$child['id']; ?>" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<hr/>
<?php 
include 'includes/footer.php';
?>