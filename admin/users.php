<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';
include './includes/head.php';
include './includes/navbar.php';

if(!is_logged_in()){
    login_error_redirect();
}

if(!has_permission()){
    permission_error_redirect();
}

if(isset($_GET['delete'])){
    $delete_id = (int)$_GET['delete'];
    $delete_id = sanitize($delete_id);
    $db->query("DELETE FROM `users` WHERE `id` = '$delete_id' ");
    // $_SESSION['error_flash'] = "User has been deleted!";
    header("Location: users.php");
}

$userQuery = $db->query("SELECT * FROM `users` ORDER BY `permissions`, `full_name`");

if(isset($_GET['add']) || isset($_GET['edit'])){
    $name = (isset($_POST['name-isset']))? $_POST['name-isset'] : "";
    $email = (isset($_POST['email-isset']))? $_POST['email-isset'] : "";
    $password = (isset($_POST['pass-isset']))? $_POST['pass-isset'] : "";
    $conf_password = (isset($_POST['conf-isset']))? $_POST['conf-isset'] : "";
    $permissions = (isset($_POST['permission-isset']))? $_POST['permission-isset'] : "";
    $errors =array();

    if(isset($_GET['edit'])){
        $edit_id = sanitize((int)$_GET['edit']);
        $editQuery = $db->query("SELECT * FROM users WHERE id = '$edit_id' ");
        $edit = mysqli_fetch_assoc($editQuery);

        $name = (isset($_POST['name-isset']) AND !empty($_POST['name-isset']))? $_POST['name-isset'] : $edit['full_name'];
        $email = (isset($_POST['email-isset']) AND !empty($_POST['email-isset']))? $_POST['email-isset'] : $edit['email'];
        $password = (isset($_POST['pass-isset']) AND !empty($_POST['pass-isset']))? $_POST['pass-isset'] : $edit['password'];
        $conf_password = (isset($_POST['conf-isset']) AND !empty($_POST['conf-isset']))? $_POST['conf-isset'] : $edit['password'];
        $permissions = (isset($_POST['permission-isset']) AND !empty($_POST['permission-isset']))? $_POST['permission-isset'] : $edit['permissions'];
    }

    if($_POST){
        $emailQuery = $db->query("SELECT * FROM `users` WHERE `email` = '$email' ");
        $emailCount = mysqli_num_rows($emailQuery);

        if(($emailCount != 0) AND (isset($_GET['add']))){
            $errors[] = 'That email address already exists in the database';
        }

        $required = array('name-isset', 'email-isset', 'pass-isset', 'conf-isset', 'permission-isset');
        foreach($required as $req){
            if(empty($_POST[$req]) AND (isset($_GET['add']))){
                $errors[] = "You must fill in all the fields";
                break;
            }
        }

        if(strlen($password) < 6){
            $errors[] = 'Your password must be at least 6 Characters long';
        }

        if($password != $conf_password){
            $errors[] = 'Password does not match, Please try again.';
        }

        if(!empty($errors)){
            echo display_errors($errors);
        }else{
            // ADD USER TO THE DATABASE
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insertSQL = "INSERT INTO `users` (`full_name`, `email`, `password`, `permissions`) Values ('$name', '$email', '$hashed', '$permissions')";
            $_SESSION['success_flash'] = "New user has been added!";
            
            // EDIT USER TO THE DATABASE
            if(isset($_GET['edit'])){
                $insertSQL = "UPDATE `users` SET `full_name` = '$name', `email` = '$email', `permissions` = '$permissions' WHERE id = '$edit_id'";
                $_SESSION['success_flash'] = "User Data has been edited!";
            }
            $db->query($insertSQL);
            header("Location: users.php");
        }
    }
    ?>
    <h1 class="text-center text-uppercase"><?= ((isset($_GET['edit'])) ? "Edit A " : "Add A ");?>User</h1>
    <hr>
    <form action="users.php?<?=((isset($_GET['edit']))? 'edit='.$edit_id : "add=1");?>" method="post" class="p-5 d-flex flex-wrap">
        <div class="form-group col-md-6">
            <label for="name-id">Full Name:</label>
            <input type="text" class="form-control" name="name-isset" id="name-id" value="<?=$name;?>">
        </div>
        <div class="form-group col-md-6">
            <label for="email-id">Email:</label>
            <input type="email" class="form-control" name="email-isset" id="email-id" value="<?=$email;?>">
        </div>
        <?php if(isset($_GET['edit'])): ?>
        <?php else: ?>
        <div class="form-group col-md-6">
            <label for="pass-id">Password:</label>
            <input type="<?=((isset($_GET['edit'])) ? "text" : "password");?>" class="form-control" name="pass-isset" id="pass-id" value="<?=$password;?>" <?=((isset($_GET['edit'])) ? "readonly" : "");?>>
        </div>
        <div class="form-group col-md-6">
            <label for="conf-id">Confirm Password:</label>
            <input type="<?=((isset($_GET['edit'])) ? "text" : "password");?>" class="form-control" name="conf-isset" id="conf-id" value="<?=$conf_password;?>" <?=((isset($_GET['edit'])) ? "readonly" : "");?>>
        </div>
        <?php endif; ?>
        <div class="form-group col-md-6">
            <label for="permission-id">Permisions:</label>
            <select name="permission-isset" id="permission-id" class="form-control">
                <option value="" <?=(($permissions == '') ? ' selected' : '');?>></option>
                <option value="admin" <?=(($permissions == 'admin') ? ' selected' : '');?>>Admin</option>
                <option value="editor" <?=(($permissions == 'editor') ? ' selected' : '');?>>Editor</option>
            </select>
        </div>
        <div class="form-group col-md-6 text-right mt-5">
            <a href="users.php" class="btn btn-secondary">Cancel</a>
            <input type="submit" value="<?=((isset($_GET['edit'])) ? "EDIT " : "ADD ");?>USER" class="btn btn-primary">
        </div>  
    </form>
<?php
    }else{

    if(isset($_SESSION['success_flash'])){
        echo '<div class="bg-success"><p class="text-white text-center">'.$_SESSION['success_flash'].'</p></div>';
        unset($_SESSION['success_flash']);
    }
?>

<h1 class="text-center text-uppercase">Users</h1>

<hr/>

<a href="users.php?add=1" class="btn btn-success float-right mx-5 mb-4" id="big-font">Add New User</a>
<div class="p-5">
    <table class="table table-bordered table-striped">
        <thead class="bg-warning">
            <th>Edit / Delete</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Join Date</th>
            <th>Last Login</th>
            <th>Permissions</th>
        </thead>
        <tbody>
            <?php while($users = mysqli_fetch_assoc($userQuery)) : ?>
                <tr>
                    <td>
                        <a href="users.php?edit=<?=$users['id'];?>" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>
                        <?php if($users['id'] != $user_data['id']): ?>
                            <a href="users.php?delete=<?=$users['id'];?>" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
                        <?php endif; ?>
                    </td>
                    <td><?=$users['full_name'];?></td>
                    <td><?=$users['email'];?></td>
                    <td><?=pretty_date($users['join_date']);?></td>
                    <td><?=($users['last_login'] == "0000-00-00 00:00:00")?"Never Logged In" : pretty_date($users['last_login']);?></td>
                    <td><?=$users['permissions'];?></td>
                </tr>
            <?php endwhile;?>
        </tbody>
    </table>
</div>

<?php }
    include './includes/footer.php';
?>