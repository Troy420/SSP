<nav id="navigation" class="navbar navbar-expand-md">
    <div class="container">
        <a href="/ssp/admin/index.php" class="navbar-brand"><?=(has_permission('admin')) ? 'Admin' : 'Users' ?></a>
        <ul class="nav navbar-nav">
            <li><a class="nav-link" href="/ssp/admin/brand.php">Brands</a></li>
            <li><a class="nav-link" href="/ssp/admin/categories.php">Categories</a></li>
            <li><a href="./products.php" class="nav-link">Products</a></li>
            <li><a href="./archive.php" class="nav-link">Archived</a></li>
            <?php if(has_permission('admin')): ?>
                <li><a class="nav-link" href="/ssp/admin/users.php">Users</a></li>
            <?php endif; ?>
            <li class="dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><?= "Hello, " .$user_data['first']; ?></a>
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="change_password.php" class="nav-link">Change Password</a></li>
                        <li><a href="logout.php" class="nav-link">Log out</a></li>
                    </ul>
                </li>
            </li>
        </ul>
    </div>
</nav>