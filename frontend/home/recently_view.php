<?php
    session_start();
    ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<?php
include "../common/header_js.php";
?>
<body>
<?php
    include('../../configs/constant.php');
    include('../../configs/connect.php');
    include('../common/functions.php');

    include "../common/navigation.php";
    include "../common/branding.php";
    include "../common/menu.php";
    include "../common/slider.php";

    // Get list ID viewed product cookie
    $viewedProducts = '';

    if (isset($_COOKIE['viewed_products'])) {
        $viewedProducts = json_decode($_COOKIE['viewed_products'], true);
    } else {
        echo '';
    }

    // Create numpage, check page
    $numPage = 12;
    $page = '';
    if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) {
        $page = (int) $_GET['page'];
    } else {
        $page = 1;
    }

    $startPage = ($page - 1) * $numPage;
?>

    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <?php
                // Get cookie viewed_products
                if (is_array($viewedProducts) && count($viewedProducts) > 0) {
                    $viewedProducts = implode(",", $viewedProducts);
                    $queryViewed = "SELECT * FROM products WHERE id IN ($viewedProducts)  LIMIT $startPage, $numPage";
                    $stmt = mysqli_prepare($conn, $queryViewed);
                    mysqli_stmt_execute($stmt);
                    $resultViewed = mysqli_stmt_get_result($stmt);

                    while ($productViewed = mysqli_fetch_assoc($resultViewed)) {
                ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="single-product">
                                <div class="product-f-image">
                                    <?php echo '<img src="../../backend/theme/upload/product/' . $productViewed['image'] . ' ">' ?>
                                    <div class="product-hover">

                                       
                                        <a href="single_product.php?id=<?php echo $productViewed['id']  ?>" class="view-details-link"><i class="fa fa-link"></i> See details</a>
                                    </div>
                                </div>

                                <h2><a href="single_product.php?id=<?php echo $productViewed['id']  ?>"><?php echo $productViewed['name']; ?></a></h2>

                                <div class="product-carousel-price">
                                    <ins><?php echo '$' . number_format($productViewed['price']); ?></ins>
                                    <del><?php echo '$' . number_format($productViewed['old_price']); ?></del>
                                </div>
                            </div> <br>
                        </div>
                <?php
                    }
                } else {
                    echo '<h2 style="text-align:center">There are currently no products</h2>';
                }

                // Pagiantion
                $querySql = '';
                $totalRecord = '';
                $totalPage = '';

                $queryViewed = "SELECT * FROM products WHERE id IN ($viewedProducts)";
                $stmt = mysqli_prepare($conn, $queryViewed);
                mysqli_stmt_execute($stmt);

                $resultPage = mysqli_stmt_get_result($stmt);
                $totalRecord = mysqli_num_rows($resultPage);
                $totalPage = ceil($totalRecord / $numPage);
                ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="product-pagination text-center">
                            <nav>
                                <ul class="pagination">

                                    <!-- Previous -->
                                    <?php
                                    if ($page > 1) {
                                    ?>
                                        <li>
                                            <a href="recently_view.php?page=<?php echo ($page - 1) ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php  } ?>

                                    <!-- Number page -->
                                    <?php
                                    $getPgaeUrl = isset($_GET['page']) ?  $_GET['page'] : '';

                                    if ($totalRecord > $numPage) {
                                        for ($i = 1; $i <= $totalPage; $i++) {

                                            if ($getPgaeUrl == $i) {
                                                echo '   <li class="active"><a href="recently_view.php?page=' . $i . '" class="active">' . $i . '</a></li>';
                                            } else {
                                                echo '<li><a href="recently_view.php?page=' . $i . '">' . $i . '</a></li>';
                                            }
                                        }
                                    } else {
                                        echo '';
                                    }
                                    ?>

                                    <!-- Next -->
                                    <?php
                                    if ($page < $totalPage) {
                                    ?>
                                        <li>
                                            <a href="recently_view.php?page=<?php echo ($page + 1) ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>

                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    ob_end_flush();
    include "../common/footer_top.php";
    include "../common/footer_bottom.php";
    include "../common/footer_js.php";
?>
</body>
</html>