<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';
    include './includes/head.php';

    // $password = "123456"; "password";
    // $hashed = password_hash($password, PASSWORD_DEFAULT);
    // echo $hashed;

    $email = (isset($_POST['email-isset']))? sanitize($_POST['email-isset']) : "";
    $email = trim($email);
    $password = (isset($_POST['password-isset']))? sanitize($_POST['password-isset']) : "";
    $password = trim($password);
    $errors = array();
?>

<div id="form-container">
    <form action="login.php" method="post">
        
        <h3 class="text-center">LOGIN</h3>
        <div class="underline"></div>

        <?php   
            if(isset($_SESSION['error_flash'])){
                echo '<div class="bg-danger"><p class="text-white text-center">'.$_SESSION['error_flash'].'</p></div>';
                unset($_SESSION['error_flash']);
            }

        ?>

        <a href="/ssp/index.php"><i class="glyphicon glyphicon-arrow-left"></i></a>

        <div>
            <?php 
                if($_POST){
                    // FORM VALIDATION
                    if(empty($_POST['email-isset']) || empty($_POST['password-isset'])){
                        $errors[] = 'You must provide the correct email address and password';
                    }

                    // PASSWORD IS MORE THAN 6 CHARACTERS
                    if(strlen($password) < 6){
                        $errors[] = "Password must be at least 6 characters";
                    }

                    // CHECK IF EMAIL EXIST IN THE DATABASE
                    $emailQuery = $db->query("SELECT * FROM users WHERE email = '$email' ");
                    $user = mysqli_fetch_assoc($emailQuery);
                    $userCount = mysqli_num_rows($emailQuery); 
                    if($userCount < 1){
                        $errors[] = "That email address does not exist in the database";
                    }

                    // VERIFY PASSWORD
                    if(!password_verify($password, $user['password'])){
                        $errors[] = "Password does not match. Please try again";
                    }

                    // CHECK ERRORS
                    if(!empty($errors)){
                        echo display_errors($errors);
                    }else{
                        // LOG USER IN
                        $user_id = $user['id'];
                        login($user_id);
                    }
                }
            ?>
        </div>

        <div class="form-group">
            <label for="email-id">Email: </label>
            <input type="email" name="email-isset" id="email-id" class="form-control" value="<?=$email;?>">  
        </div>
        <div class="form-group">
            <label for="password-id">Password: </label>
            <input type="password" name="password-isset" id="pasword-id" class="form-control" value="<?=$password;?>">  
        </div>
        <div class="form-group text-center">
            <input type="submit" value="login" class="primary-btn">
        </div>
    </form>
    
</div>

<?php 
    include './includes/footer.php';
?>




