<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<?php
include "../common/header_js.php";

// Get list ID viewed product
$viewedProducts = '';

if (isset($_COOKIE['viewed_products'])) {
    $viewedProducts = json_decode($_COOKIE['viewed_products'], true);
} else {
    echo '';
}

$page =1;
?>

<body>
<?php
    const LIMIT_TOP = 3;

    include('../../configs/constant.php');
    include('../../configs/connect.php');
    include('../common/functions.php');

    include "../common/navigation.php";
    include "../common/branding.php";
    include "../common/menu.php";
    include "../common/slider.php";

    // Message change password success
    flashMessage();
?>
    <div class="promo-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="single-promo promo1">
                        <i class="fa fa-refresh"></i>
                        <p>30 Days return</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="single-promo promo2">
                        <i class="fa fa-truck"></i>
                        <p>Free shipping</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="single-promo promo3">
                        <i class="fa fa-lock"></i>
                        <p>Secure payments</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="single-promo promo4">
                        <i class="fa fa-gift"></i>
                        <p>New products</p>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End promo area -->

    <div class="maincontent-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="latest-product">
                        <h2 class="section-title">Latest Products</h2>
                        <div class="product-carousel">
                            <?php
                            $query = queryAll('products', ' active = ' . ACTIVE . '', 'sort_order', 'ASC');
                            $resultProducts = mysqli_query($conn, $query);
                            $numRows = mysqli_num_rows($resultProducts);
                            if ($numRows > 0) {
                                while ($row = mysqli_fetch_array($resultProducts)) {
                            ?>
                                <div class="single-product">
                                    <div class="product-f-image">
                                        <?php echo '<img src="../../backend/theme/upload/product/' . $row['image'] . ' ">' ?>
                                        <div class="product-hover">

                                            
                                            <a href="single_product.php?id=<?php echo $row['id']  ?>" class="view-details-link"><i class="fa fa-link"></i> See details</a>
                                        </div>
                                    </div>

                                    <h2><a href="single_product.php?id=<?php echo $row['id']  ?>"><?php echo $row['name']; ?></a></h2>

                                    <div class="product-carousel-price">
                                        <ins><?php echo '$' . number_format($row['price']); ?></ins>
                                        <del><?php echo '$' . number_format($row['old_price']); ?></del>
                                    </div>
                                </div>
                            <?php
                                }
                            } else {
                                echo '';
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End main content area -->

    <div class="brands-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="brand-wrapper">
                        <div class="brand-list">
                            <?php
                            $query = queryAll('brands', ' active = ' . ACTIVE . '', 'sort_order', 'ASC');
                            $resultBrands = mysqli_query($conn, $query);
                            $numRows = mysqli_num_rows($resultBrands);

                            if ($numRows > 0) {
                                while ($row = mysqli_fetch_array($resultBrands)) {
                            ?>
                                    <a href="./brand_product.php?brand_id=<?php echo $row['id'];?>&page=<?php echo $page ;?>">
                                        <?php echo '<img src="../../backend/theme/upload/brand/' . $row['image_url'] . ' ">' ?>
                                    </a>

                            <?php }
                            } else {
                                echo '';
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End brands area -->

    <div class="product-widget-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="single-product-widget">
                        <h2 class="product-wid-title">Top Sellers</h2>
                        <a href="./top_seller.php?page=<?php echo $page ;?>" class="wid-view-more">View All</a>

                        <?php
                        $queryProducts = "SELECT * FROM products WHERE is_best_sell = " . ACTIVE . " AND active = " . ACTIVE . " ORDER BY RAND() LIMIT " . LIMIT_TOP . " ";
                        $resultSellers = mysqli_query($conn, $queryProducts);
                        $numRows = mysqli_num_rows($resultSellers);

                        if ($numRows > 0) {
                            while ($row = mysqli_fetch_array($resultSellers)) {
                        ?>
                                <div class="single-wid-product">
                                    <a href="single_product.php?id=<?php echo $row['id'] ;?>&page=<?php echo $page ;?>">
                                        <?php echo '<img src="../../backend/theme/upload/product/' . $row['image'] . ' " class="product-thumb">' ?>
                                    </a>

                                    <h2><a href="single_product.php?id=<?php echo $row['id'] ;?>"><?php echo $row['name']; ?></a></h2>
                                    <div class="product-wid-rating">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>

                                    <div class="product-wid-price">
                                        <ins><?php echo '$' . number_format($row['price']); ?></ins>
                                        <del><?php echo '$' . number_format($row['old_price']); ?></del>
                                    </div>
                                </div>

                        <?php
                            }
                        } else {
                            echo '';
                        }
                        ?>

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="single-product-widget">
                        <h2 class="product-wid-title">Recently Viewed</h2>
                        <a href="./recently_view.php?page=<?php echo $page ;?>" class="wid-view-more">View All</a>
                        <?php

                        // Get cookie viewed_products
                        if (is_array($viewedProducts) && count($viewedProducts) > 0) {
                            $viewedProducts = implode(",", $viewedProducts);
                            $queryViewed = "SELECT * FROM products WHERE id IN ($viewedProducts) ORDER BY RAND() LIMIT " . LIMIT_TOP . "";
                            $stmt = mysqli_prepare($conn, $queryViewed);
                            mysqli_stmt_execute($stmt);
                            $resultViewed = mysqli_stmt_get_result($stmt);

                            while ($productViewed = mysqli_fetch_assoc($resultViewed)) {
                        ?>
                                <div class="single-wid-product">
                                    <a href="single_product.php?id=<?php echo $productViewed['id'] ;?>">
                                        <?php echo '<img src="../../backend/theme/upload/product/' . $productViewed['image'] . ' " class="product-thumb">' ?>
                                        <h2><a href="single_product.php?id=<?php echo $productViewed['id']  ?>"><?php echo $productViewed['name']; ?></a></h2>
                                        <div class="product-wid-rating">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div class="product-wid-price">
                                            <ins><?php echo '$' . number_format($productViewed['price']); ?></ins>
                                            <del><?php echo '$' . number_format($productViewed['old_price']); ?></del>
                                        </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo '';
                        }
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="single-product-widget">
                        <h2 class="product-wid-title">Top New</h2>
                        <a href="./top_new.php?page=<?php echo $page ;?>" class="wid-view-more">View All</a>

                        <?php
                        $queryNewProducts = "SELECT * FROM products WHERE is_new = " . ACTIVE . " AND active = " . ACTIVE . " ORDER BY RAND() LIMIT " . LIMIT_TOP . " ";
                        $resultSellers = mysqli_query($conn, $queryNewProducts);
                        $numRows = mysqli_num_rows($resultSellers);

                        if ($numRows > 0) {
                            while ($row = mysqli_fetch_array($resultSellers)) {
                        ?>
                                <div class="single-wid-product">
                                    <a href="single_product.php?id=<?php echo $row['id'] ;?>">
                                        <?php echo '<img src="../../backend/theme/upload/product/' . $row['image'] . ' " class="product-thumb">' ?>
                                    </a>

                                    <h2><a href="single_product.php?id=<?php echo $row['id']  ?>"><?php echo $row['name']; ?></a></h2>
                                    <div class="product-wid-rating">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>

                                    <div class="product-wid-price">
                                        <ins><?php echo '$' . number_format($row['price']); ?></ins>
                                        <del><?php echo '$' . number_format($row['old_price']); ?></del>
                                    </div>

                                </div>
                        <?php
                            }
                        } else {
                            echo '';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End product widget area -->

    <?php
    include "../common/footer_top.php";
    include "../common/footer_bottom.php";
    include "../common/footer_js.php";
    ?>
    <script type="text/javascript" src="https://web.cmbliss.com/webtools/hotline/js/hotline.js"></script><script type="text/javascript">$("body").hotline({phone:"0396832140",p_right:true,p_bottom:true,bottom:50,p_left:false,left:NaN,bg_color:"#e60808",abg_color:"rgba(230, 8, 8, 0.7)",show_bar:false,position:"fixed",});</script>
     <!-- <script type="text/javascript" src="https://web.cmbliss.com/webtools/hotline/js/hotline.js"></script>

     <script type="text/javascript">
        //    <div class="phone">
                $("body").hotline({
                    phone:"0396832140",
                    p_bottom:true,
                    // bottom:0,
                    p_left:true,
                    // left:0,
                    top:100,
                    bg_color:"#e60808",
                    abg_color:"rgba(230, 8, 8, 0.7)",
                    show_bar:true,position:"absolute",
                 });
        //    </div>
        </script> -->
        <!-- <script type="text/javascript" src="https://web.cmbliss.com/webtools/hotline/js/hotline.js"></script><script type="text/javascript">$("body").hotline({phone:"0396832140",p_top:true,top:2650,p_right:true,right:0,p_bottom:false,bottom:NaN,p_left:false,left:NaN,bg_color:"#e60808",abg_color:"rgba(230, 8, 8, 0.7)",show_bar:true,position:"absolute",
        
        });</script> -->
       <!-- <script type="text/javascript" src="https://web.cmbliss.com/webtools/hotline/js/hotline.js"></script><script type="text/javascript">$("body").hotline({phone:"0986822596",p_bottom:true,bottom:0,p_left:true,left:0,bg_color:"#e60808",abg_color:"rgba(230, 8, 8, 0.7)",show_bar:true,position:"absolute",});</script> -->
        <!-- <style>

#floating-phone { display: none; position: fixed; left: 10px; bottom: 10px; height: 50px; width: 50px; background: #46C11E url(http://callnowbutton.com/phone/callbutton01.png) center / 30px no-repeat; z-index: 99; color: #FFF; font-size: 35px; line-height: 55px; text- align: center; border-radius: 50%; -webkit-box-shadow: 0 2px 5px rgba(0,0,0,.5); -moz-box-shadow: 0 2px 5px rgba(0,0,0,.5); box-shadow: 0 2px 5px rgba(0,0,0,.5); }

@media (max-width: 650px) { #floating-phone { display: block; } }

</style>

 

<a href=”tel:0942691366″ title=”Gọi 0942691366″ onclick=”_gaq.push([‘_trackEvent’, ‘Contact’, ‘Call Now Button’, ‘Phone’]);” id=”floating-phone”>

<i class=”uk-icon-phone”></i></a>  -->
<!-- <script type="text/javascript" src="https://web.cmbliss.com/webtools/hotline/js/hotline.js"></script><script type="text/javascript">$("body").hotline({phone:"0396832140",p_right:true,p_bottom:true,bottom:0,p_left:false,left:NaN,bg_color:"#e60808",abg_color:"rgba(230, 8, 8, 0.7)",show_bar:false,position:"fixed",});</script> -->
<!-- <script type="text/javascript" src="https://web.cmbliss.com/webtools/hotline/js/hotline.js"></script><script type="text/javascript">$("body").hotline({phone:"0986822596",p_bottom:true,bottom:0,p_right:true,right:0,bg_color:"#e60808",abg_color:"rgba(230, 8, 8, 0.7)",show_bar:false,position:"fixed",});</script> -->
<!-- <script type="text/javascript" src="https://web.cmbliss.com/webtools/hotline/js/hotline.js"></script><script type="text/javascript">$("body").hotline({phone:"0396832140",p_bottom:true,bottom:0,p_right:true,right:0,bg_color:"#e60808",abg_color:"rgba(230, 8, 8, 0.7)",show_bar:false,position:"fixed",});</script> -->
        <!-- code duowis laf ben trai -->
</body>

</html>