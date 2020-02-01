<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';
    include './includes/head.php';

    // $password = "123456"; "password";
    // $hashed = password_hash($password, PASSWORD_DEFAULT);
    // echo $hashed;

    // ERROR ARRAY
    $errors = array();

    $user_id = $user_data['id'];
    $hashed = $user_data['password'];
    $curr_password = (isset($_POST['curr-pass-isset']))? sanitize($_POST['curr-pass-isset']) : "";
    $curr_password = trim($curr_password);
    $new_password = (isset($_POST['new-pass-isset']))? sanitize($_POST['new-pass-isset']) : "";
    $new_password = trim($new_password);
    $conf_password = (isset($_POST['conf-pass-isset']))? sanitize($_POST['conf-pass-isset']) : "";
    $conf_password = trim($conf_password);
    $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
?>

<div id="form-container">
    <form action="change_password.php" method="post">
        
        <h3 class="text-center">Change Password</h3>
        <div class="underline"></div>

        <a href="/ssp/admin/index.php"><i class="glyphicon glyphicon-arrow-left"></i></a>

        <div>
            <?php 
                if($_POST){
                    // FORM VALIDATION
                    if(empty($_POST['curr-pass-isset']) || empty($_POST['new-pass-isset']) || empty($_POST['conf-pass-isset']) ){
                        $errors[] = 'You must fill in all of the forms';
                    }

                    // PASSWORD IS MORE THAN 6 CHARACTERS
                    if(strlen($new_password) < 6){
                        $errors[] = "Password must be at least 6 characters";
                    }

                    // VERIFY CURRENT PASSWORD
                    if(!password_verify($curr_password, $hashed)){
                        $errors[] = "Current Password does not Match. Please Try Again";
                    }

                    if($new_password != $conf_password){
                        $errors[] = "Your New Password does not Match";
                    }

                    // CHECK ERRORS
                    if(!empty($errors)){
                        echo display_errors($errors);
                    }else{
                        // UPDATE PASSWORD
                        $db->query("UPDATE `users` SET `password` = '$new_hashed' WHERE `id` = '$user_id' ");
                        $_SESSION["success_flash"] = "Your Password Has Been Updated!";
                        header("Location: index.php");
                    }
                }
            ?>
        </div>

        <div class="form-group">
            <label for="curr-pass-id">Current Password: </label>
            <input type="password" name="curr-pass-isset" id="curr-pass-id" class="form-control" value="<?=$curr_password;?>">  
        </div>
        <div class="form-group">
            <label for="new-pass-id">New Password: </label>
            <input type="password" name="new-pass-isset" id="new-pass-id" class="form-control" value="<?=$new_password;?>">  
        </div>
        <div class="form-group">
            <label for="conf-pass-id">Confirm Password: </label>
            <input type="password" name="conf-pass-isset" id="conf-pass-id" class="form-control" value="<?=$conf_password;?>">  
        </div>
        <div class="form-group text-center">
            <input type="submit" value="Confirm" class="primary-btn">
        </div>
    </form>
    
</div>

<?php 
    include './includes/footer.php';
?>