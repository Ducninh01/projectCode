<div class="site-branding-area">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="logo">
                    <h1><a href="../home/"><img src="../img/logo-anbico.png"></a></h1>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="shopping-item">
                    <a href="../cart/show_cart.php">Cart<span class="cart-amunt"></span>
                        <i class="fa fa-shopping-cart"></i> <span class="product-count">
                            <?php
                            if (isset($_SESSION['save_to_cart']) && $_SESSION['save_to_cart'] > 0) {
                                echo count($_SESSION['save_to_cart']);
                            } else {
                                echo 0;
                            }
                            ?>
                        </span></a>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End site branding area -->