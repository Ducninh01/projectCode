<?php
    session_start();
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

    // Create numpage, check page
    $numPage = 12;

    $page = '';
    if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) {
        $page = (int) $_GET['page'];
    } else {
        $page = 1;
    }

    $startPage = ($page - 1) * $numPage;
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_best_sell =" . ACTIVE . " AND active = " . ACTIVE . " ORDER BY sort_order ASC LIMIT ?, ?");
    $stmt->bind_param("ii", $startPage, $numPage);
    $stmt->execute();
    $resultProducts = $stmt->get_result();

?>
    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <?php
                if ($numRows > 0) {
                    while ($row = mysqli_fetch_array($resultProducts)) {
                ?>
                    <div class="col-md-3 col-sm-6">
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

                $querySql = "SELECT * FROM products WHERE is_best_sell =" . ACTIVE . " AND active =" . ACTIVE . " ";
                $resultPage = mysqli_query($conn, $querySql);
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
                                            <a href="top_seller.php?page=<?php echo ($page - 1) ?>" aria-label="Previous">
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
                                                echo '<li class="active"><a href="top_seller.php?page=' . $i . '" class="active">' . $i . '</a></li>';
                                            } else {
                                                echo '<li><a href="top_seller.php?page=' . $i . '">' . $i . '</a></li>';
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
                                            <a href="top_seller.php?page=<?php echo ($page + 1) ?>" aria-label="Next">
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
    include "../common/footer_top.php";
    include "../common/footer_bottom.php";
    include "../common/footer_js.php";
?>
</body>
</html>