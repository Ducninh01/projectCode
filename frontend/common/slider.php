<?php
$currentUrl = getCurrentURl();
if ($currentUrl == 'index.php') {
?>

    <div class="slider-area">
        <!-- Slider -->
        <div class="block-slider block-slider4">
            <ul class="" id="bxslider-home4">
                <?php
                $query = queryAll('banners', ' active = ' . ACTIVE . '', 'sort_order', 'ASC');
                $resultBanners = mysqli_query($conn, $query);
                $numRows = mysqli_num_rows($resultBanners);
                if ($numRows > 0) {
                    while ($row = mysqli_fetch_array($resultBanners)) {
                ?>
                        <li>
                            <?php echo '<img src="../../backend/theme/upload/banner/' . $row['image_url'] . ' ">' ?>
                            <div class="caption-group">
                                <h2 class="caption title">
                                    <span class="primary"> <strong> <?php echo $row['title']; ?></strong></span>
                                </h2>
                                <h4 class="caption subtitle"><?php echo $row['content']; ?></h4>
                            </div>
                        </li>

                    <?php
                    }
                } else {
                    echo '';
                }
                ?>
            </ul>
        </div>
        <!-- ./Slider -->
    </div> <!-- End slider area -->
<?php  }
if (
    $currentUrl == 'shop.php' || $currentUrl == 'single_product.php'
    || $currentUrl == 'top_new.php' || $currentUrl == 'top_seller.php' || $currentUrl == 'recently_view.php'
    || $currentUrl == 'brand_product.php' || $currentUrl == 'category_product.php'

) { ?>

    <div class="product-big-title-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-bit-title text-center">
                        <h2>Shop</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php  }

if ($currentUrl == 'show_cart.php' || $currentUrl == 'checkout.php') { ?>
    <div class="product-big-title-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-bit-title text-center">
                        <h2>Shopping Cart</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php  } ?>