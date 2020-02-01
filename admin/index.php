<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

    if(!is_logged_in()){
        login_error_redirect();
        // header("Location: login.php");
    }

    // if(!has_permission()){
    //     permission_error_redirect();
    // }

    include './includes/head.php';
    include './includes/navbar.php';

    if(isset($_SESSION['success_flash'])){
        echo '<div class="bg-success"><p class="text-white text-center">'.$_SESSION['success_flash'].'</p></div>';
        unset($_SESSION['success_flash']);
    }

    
    if(isset($_SESSION['error_permission_flash'])){
        echo '<div class="bg-danger"><p class="text-white text-center">'.$_SESSION['error_permission_flash'].'</p></div>';
        unset($_SESSION['error_permission_flash']);
    }
   
?>

<h1 class="text-uppercase text-center admin-h1">
<?php 
    echo "Welcome back to your page, " .$user_data['full_name']. "!";
?>
</h1>

<?php 
    include './includes/footer.php';
?>