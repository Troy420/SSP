<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';

    $name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $street = sanitize($_POST['street']);
    $street2 = sanitize($_POST['street2']);
    $city = sanitize($_POST['city']);
    $zip_code = sanitize($_POST['zip-code']);
    $phone_no = sanitize($_POST['phone-no']);
    $errors = array();

    $required = array(
        'full_name' => 'Full Name',
        'email' => 'Email',
        'street' => 'Street Address',
        'city' => 'City',
        'zip-code' => 'Zip Code',
        'phone-no' => 'Phone No',
    );


    // Check if all required fields are filled out
    foreach($required as $field => $display){
        if(empty($_POST[$field]) || $_POST[$field] == "" ){
            $errors[] = $display.' is required.';
        }
    }

    // CHECK IF VALID EMAIL ADDRESS
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = 'Please enter a valid emaill address';
    }

    if(!empty($errors)){
        echo display_errors($errors);
    }else{
        echo 'passed';
    }
?>