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

    $getTagUrl = isset($_GET['tags']) ? $_GET['tags'] : '';

    $stmt = $conn->prepare("SELECT * FROM products WHERE (active = " . ACTIVE . " AND tags = ?) ORDER BY sort_order ASC LIMIT ?, ?");
    $stmt->bind_param("sii", $getTagUrl, $startPage, $numPage);
    $stmt->execute();
    $resultProducts = $stmt->get_result();
    $numRows = mysqli_num_rows($resultProducts);

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

                if (!empty($getTagUrl)) {
                    $querySql = "SELECT * FROM products WHERE (active = " . ACTIVE . " AND tags = ?) ORDER BY sort_order ASC";

                    $stmt = mysqli_prepare($conn, $querySql);
                    mysqli_stmt_bind_param($stmt, "s", $getTagUrl);
                    mysqli_stmt_execute($stmt);
                    $resultPage = mysqli_stmt_get_result($stmt);
                    $totalRecord = mysqli_num_rows($resultPage);
                }

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
                                            <a href="tags.php?tags=<?php echo $getTagUrl; ?>&page=<?php echo ($page - 1) ?>" aria-label="Previous">
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
                                                echo '   <li class="active"><a href="tags.php?tags=' . $getTagUrl . '&page=' . $i . '" class="active">' . $i . '</a></li>';
                                            } else {
                                                echo '<li><a href="tags.php?tags=' . $getTagUrl . '&page=' . $i . '">' . $i . '</a></li>';
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
                                            <a href="tags.php?tags=<?php echo $getTagUrl; ?>&page=<?php echo ($page + 1) ?>" aria-label="Next">
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