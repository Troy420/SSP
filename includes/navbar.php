<?php
$sql = "SELECT * FROM categories WHERE parent = 0";
$pquery = $db->query($sql);
?>

<nav id="navigation" class="navbar navbar-expand-md">

        <!-- LOGO -->
        <a href="index.php" class="navbar-brand">SIMPLICITY</a>
        <!-- END LOGO -->

        <!-- LEFT MENU ITEMS -->
        <div class="left-menu-items">
            <ul>
                <?php while($parent = mysqli_fetch_assoc($pquery)): ?>
                    <?php 
                        $parent_id = $parent['id']; 
                        $sql2 = "SELECT * FROM categories WHERE parent = '$parent_id'";
                        $cquery = $db->query($sql2);
                    ?>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $parent['category']; ?></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php while($child = mysqli_fetch_assoc($cquery)) : ?>
                                <li><a href="category.php?category=<?=$child['id']?>">
                                    <?php echo $child['category'];?></a></li>
                            <?php endwhile; ?>
                        </ul>
                    </li>
                <?php endwhile; ?>   
                
            </ul>
        </div>
        <!-- END LEFT SIDE BAR -->

        <!-- BURGER -->
        <!-- <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button> -->
        <!-- BURGER -->

        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- SEARCH BAR -->
            <form class="form-inline">
                <input class="form-control mr-sm-2" type="search" aria-label="Search">
                <button class="btn my-2 my-sm-0" type="submit">Search</button>
            </form>
            <!-- END SEARCH BAR -->

            <!-- SHOPPING CART -->
            <ul class="navbar-nav ml-auto">
                <!-- 
                HOW ABOUT LOGIN?
                    <li class="nav-item active">
                        <a class="nav-link" href="/ssp/admin/login.php">Login</span></a>
                    </li>
                AND HELP?
                    <li class="nav-item">
                        <a class="nav-link" href="#">Help</a>
                    </li> -->

                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <span>My Cart</span>
                        <i class="fas fa-cart-arrow-down fa-4x mr-3"></i>
                    </a>
                </li>
            </ul>
            <!-- END MENU -->
        </div>
    </nav>