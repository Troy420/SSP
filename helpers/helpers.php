<?php 

function display_errors($errors){
    $display = '<ul class="bg-danger m-5" style="list-style: none">';
    foreach($errors as $error){
        $display .= '<li class="text-white text-center">'.$error.'</li>';
    }
    $display .= '</ul>';

    return $display;
}

function sanitize($dirty){
    return htmlentities($dirty, ENT_QUOTES, "UTF-8");
}

function money($amount){
    return '$'.number_format($amount, 2);
}

function login($user_id){
    $_SESSION['SBUser'] = $user_id;
    global $db;
    date_default_timezone_set("Singapore");
    $date = date("Y-m-d H:i:s");  
    $db->query("UPDATE users SET last_login = '$date' WHERE id = '$user_id' ");
    $_SESSION['success_flash'] = "You have logged in!";
    header("Location: index.php");
}

function is_logged_in(){
    if( (isset($_SESSION['SBUser'])) && ($_SESSION['SBUser'] > 0)){
        return true;
    }
    return false;
}

function login_error_redirect($url = 'login.php'){
    $_SESSION['error_flash'] = "You must be Logged in to Access the Admin page";
    header("Location: " .$url);
}

function permission_error_redirect($url = 'index.php'){
    $_SESSION['error_permission_flash'] = "You do not have the permission to Access the Users page";
    header("Location: " .$url);
}

function has_permission($permission = 'admin'){
    global $user_data;  
    $permissions = explode(',', $user_data['permissions']);

    // CHECKING WHETHER 'ADMIN' IS IN THE PERMISSIONS TABLE INSIDE THE DATABASE
    // THE 'TRUE' IS A STRICT MODE THAT ALSO CHECK THE TYPE OF THE VALUE
    if(in_array($permission, $permissions, TRUE)){
        return true;
    }
    return false;
}

function pretty_date($date){
    return date("M d, Y - H:i", strtotime($date));
}

function get_categories($child_id){
    global $db;
    $id = sanitize($child_id);
    $catQuery = $db->query("SELECT p.id AS 'pid', p.category AS 'parent', c.id AS 'cid', c.category AS 'child' 
                                    FROM categories c 
                                    INNER JOIN categories p 
                                    ON c.parent = p.id 
                                    WHERE c.id = '$id' ");
    $category = mysqli_fetch_assoc($catQuery);
    return $category;
}