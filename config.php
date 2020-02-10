<?php
define('BASEURL', $_SERVER['DOCUMENT_ROOT']);
define('CART_COOKIE',  'SIMPLICITYCOOKIE420');
define('CART_COOKIE_EXPIRE', time() + (86400 * 30));
define('TAXRATE', 0.087); // Sales tax rate set to 0 if you arent charging tax

define('CURRENCY', 'usd');
define('CHECKOUTMODE', 'TEST'); //Change test to live when you are ready to go live

if(CHECKOUTMODE == 'TEST'){
    define('STRIPE_SECRET', 'sk_test_UFieMmUHrOa4gmYYGr5cc4Lh00OkE8tFQW');
    define('STRIPE_PUBLISHABLE', 'pk_test_h8nEuP1t44Wxw8VQ5QhOEWZZ00ZEWlgMnb');
}

if(CHECKOUTMODE == 'LIVE'){
    define('STRIPE_SECRET', '');
    define('STRIPE_PUBLISHABLE', '');
}
?>