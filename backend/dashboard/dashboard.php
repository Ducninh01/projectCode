<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<?php
include "../common/header_lib.php";
?>

<body>
<?php
    include('../../configs/constant.php');
    include('../../configs/connect.php');
    include('../common/functions.php');
    include('../common/navbar_top.php');
    include('../common/sub_navbar.php');
?>
    <div class="main">
        <div class="main-inner">
            <div class="container">
                <div class="row">
                    <div class="wrapper">
                        <marquee behavior="alternate"><span class="marquee">Welcome to dashboard</span></marquee>
                    </div>
                    <div class="span12">
                        <div class="widget">
                            <div class="widget-header"><i class="icon-bookmark"></i>
                                <h3>Shortcuts</h3>
                            </div>
                            <div class="widget-content">
                                <div class="shortcuts">
                                    <a href="../category/list_category.php" class="shortcut"><i class="shortcut-icon icon-sitemap"></i>
                                        <span class="shortcut-label">Categories</span> </a>
                                    <a href="../brand/list_brand.php" class="shortcut"><i class="shortcut-icon icon-th-list"></i>
                                        <span class="shortcut-label">Brands</span></a>
                                    <a href="../product/list_product.php" class="shortcut"><i class="shortcut-icon icon-table"></i>
                                        <span class="shortcut-label">Products</span> </a>
                                    <a href="../banner/list_banner.php" class="shortcut"><i class="shortcut-icon icon-folder-open"></i>
                                        <span class="shortcut-label">Banners</span> </a>

                                    <a href="../user/list_user.php" class="shortcut"><i class="shortcut-icon icon-user"></i>
                                        <span class="shortcut-label">Users</span> </a>
                                    <a href="../customer/list_customer.php" class="shortcut"><i class="shortcut-icon icon-group "></i>
                                        <span class="shortcut-label">Customer</span> </a>
                                    <a href="../order/list_order.php" class="shortcut"><i class="shortcut-icon icon-money"></i>
                                        <span class="shortcut-label">Order details</span> </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
    include "../common/extra.php";
    include "../common/footer.php";
    include "../common/footer_lib.php";
?>
</body>
</html>