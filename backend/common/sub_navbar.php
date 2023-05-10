<?php
    $currentUrl = getCurrentURl();
?>

<div class="subnavbar">
    <div class="subnavbar-inner">
        <div class="container">
            <ul class="mainnav">
                <?php
                if ($currentUrl == 'dashboard.php') {
                    echo '<li class="active">';
                } else {
                    echo '<li>';
                }
                ?>
                <a href="../dashboard/dashboard.php">
                    <i class="icon-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                </li>

                <?php
                if ($currentUrl == 'add_category.php' || $currentUrl == 'list_category.php' || $currentUrl == 'edit_category.php') {
                    echo '<li class="active dropdown">';
                } else {
                    echo '<li class=" dropdown">';
                }
                ?>
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-sitemap"></i>
                    <span>Categories</span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../category/add_category.php">Add categories</a></li>
                    <li><a href="../category/list_category.php">List categories</a></li>
                </ul>
                </li>

                <?php
                if ($currentUrl == 'add_brand.php' || $currentUrl == 'list_brand.php' || $currentUrl == 'edit_brand.php') {
                    echo '<li class="active dropdown">';
                } else {
                    echo '<li class=" dropdown">';
                }
                ?>
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-th-list"></i>
                    <span>Brands</span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../brand/add_brand.php">Add brands</a></li>
                    <li><a href="../brand/list_brand.php">List brands</a></li>
                </ul>
                </li>

                <?php
                if (
                    $currentUrl == 'add_product.php' || $currentUrl == 'list_product.php' || $currentUrl == 'edit_product.php'
                    || $currentUrl == 'add_gallery.php' || $currentUrl == 'list_gallery.php' || $currentUrl == 'edit_gallery.php'
                ) {
                    echo '<li class="active dropdown">';
                } else {
                    echo '<li class=" dropdown">';
                }
                ?>
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-table"></i>
                    <span>Products</span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../product/add_product.php">Add products</a></li>
                    <li><a href="../product/list_product.php">List products</a></li>
                </ul>
                </li>

                <?php
                if ($currentUrl == 'add_banner.php' || $currentUrl == 'list_banner.php' || $currentUrl == 'edit_banner.php') {
                    echo '<li class="active dropdown">';
                } else {
                    echo '<li class=" dropdown">';
                }
                ?>
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-folder-open"></i>
                    <span>Banners</span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../banner/add_banner.php">Add banners</a></li>
                    <li><a href="../banner/list_banner.php">List banners</a></li>
                </ul>
                </li>

                <?php
                if ($currentUrl == 'add_user.php' || $currentUrl == 'list_user.php' || $currentUrl == 'edit_user.php') {
                    echo '<li class="active dropdown">';
                } else {
                    echo '<li class=" dropdown">';
                }
                ?>
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-user"></i>
                    <span>Users</span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../user/add_user.php">Add users</a></li>
                    <li><a href="../user/list_user.php">List users</a></li>
                </ul>
                </li>

                <?php
                if ($currentUrl == 'add_customer.php' || $currentUrl == 'list_customer.php' || $currentUrl == 'edit_customer.php') {
                    echo '<li class="active dropdown">';
                } else {
                    echo '<li class=" dropdown">';
                }
                ?>
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-group"></i>
                    <span>customers</span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../customer/add_customer.php">Add customers</a></li>
                    <li><a href="../customer/list_customer.php">List customers</a></li>
                </ul>
                </li>

                <?php
                if ($currentUrl == 'list_order.php' || $currentUrl == 'list_order_details.php') {
                    echo '<li class="active dropdown">';
                } else {
                    echo '<li class=" dropdown">';
                }
                ?>
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-money"></i>
                    <span>Order details</span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../order/list_order.php">List order details</a></li>
                </ul>
                </li>
            </ul>
        </div>
    </div>
</div>