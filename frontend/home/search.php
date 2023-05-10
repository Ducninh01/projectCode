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

    // Keyword
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : 1;

    // Create numpage, check page
    $numPage = 12;
    $page = '';
    if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) {
        $page = (int) $_GET['page'];
    } else {
        $page = 1;
    }

    $startPage = ($page - 1) * $numPage;

    //if no search keyword -> query all records products
    if ($keyword == '') {
        $query = "SELECT products.*, categories.name as category_name , brands.name as brand_name
        FROM products
        INNER JOIN categories ON products.category_id = categories.id
        INNER JOIN brands ON products.brand_id = brands.id
        WHERE (products.active = " . ACTIVE . ")
        ORDER BY products.id DESC LIMIT $startPage,$numPage";
    } else {
        //if choose search keyword -> query search key products
        $query = "SELECT products.*, categories.name as category_name , brands.name as brand_name
        FROM products
        INNER JOIN categories ON products.category_id = categories.id
        INNER JOIN brands ON products.brand_id = brands.id
        WHERE (products.active = " . ACTIVE . ")
        AND (products.name LIKE '%$keyword%' OR products.price LIKE '%$keyword%' OR products.old_price LIKE '%$keyword%' OR categories.name LIKE '%$keyword%' OR brands.name LIKE '%$keyword%')
        ORDER BY products.id DESC LIMIT $startPage,$numPage";
    }

    $resultQuery = mysqli_query($conn, $query);
    ?>
    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <?php
                $numRows = mysqli_num_rows($resultQuery);
                if ($numRows > 0) {
                    while ($row = mysqli_fetch_array($resultQuery)) {
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
                // Pagination
                $querySql = '';
                $totalRecord = '';
                // If no search then show all records in table
                if ($keyword == '') {
                    $querySql = "SELECT products.*, categories.name as category_name , brands.name as brand_name
                    FROM products
                    INNER JOIN categories ON products.category_id = categories.id
                    INNER JOIN brands ON products.brand_id = brands.id
                    WHERE (products.active = " . ACTIVE . ")
                    ORDER BY products.id DESC";
                } else {
                    // If choose keyword search , show all records search in table
                    $querySql = "SELECT products.*, categories.name as category_name , brands.name as brand_name
                    FROM products
                    INNER JOIN categories ON products.category_id = categories.id
                    INNER JOIN brands ON products.brand_id = brands.id
                    WHERE (products.active = " . ACTIVE . ")
                    AND (products.name LIKE '%$keyword%' OR products.price LIKE '%$keyword%' OR products.old_price LIKE '%$keyword%' OR categories.name LIKE '%$keyword%' OR brands.name LIKE '%$keyword%')
                    ORDER BY products.id DESC ";
                }
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
                                            <a href="search.php?keyword=<?php echo $keyword; ?>&page=<?php echo ($page - 1) ?>" aria-label="Previous">
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
                                                echo '   <li class="active"><a href="search.php?keyword=' . $keyword . '&page=' . $i . '" class="active">' . $i . '</a></li>';
                                            } else {
                                                echo '<li><a href="search.php?keyword=' . $keyword . '&page=' . $i . '">' . $i . '</a></li>';
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
                                            <a href="search.php?keyword=<?php echo $keyword; ?>&page=<?php echo ($page + 1) ?>" aria-label="Next">
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