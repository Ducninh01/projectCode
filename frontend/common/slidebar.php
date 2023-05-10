<?php
// Get list ID viewed product
$viewedProducts = '';

if (isset($_COOKIE['viewed_products'])) {
    $viewedProducts = json_decode($_COOKIE['viewed_products'], true);
} else {
    echo '';
}

const LIMIT_VIEWED = 3;
const LIMIT_SLIDEBAR  = 4;

?>
<div class="single-sidebar">
    <h2 class="sidebar-title">Search Products</h2>
    <form method="GET" action="../home/search.php">
        <input type="text" name="keyword" value="<?php echo (isset($keyword)) ? $keyword : ''; ?>" placeholder="Search products...">
        <button type="submit" class="btn btn-info search">Search</button>
    </form>
</div>

<!--------sidebar thubmnail-recent------------>
<div class="single-sidebar">
    <h2 class="sidebar-title">Products</h2>

    <?php
    $queryPs = "SELECT * FROM products WHERE active = " . ACTIVE . " ORDER BY RAND() LIMIT " . LIMIT_SLIDEBAR . " ";
    $resultProducts = mysqli_query($conn, $queryPs);
    $numRows = mysqli_num_rows($resultProducts);
    if ($numRows > 0) {
        while ($row = mysqli_fetch_array($resultProducts)) {
    ?>
            <div class="thubmnail-recent">
                <?php echo '<img src="../../backend/theme/upload/product/' . $row['image'] . ' " class="recent-thumb" alt="">' ?>
                <h2><a href="../home/single_product.php?id=<?php echo $row['id']  ?>"><?php echo $row['name']; ?></a></h2>
                <div class="product-sidebar-price">
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


<div class="single-sidebar">
    <h2 class="sidebar-title">Recently Viewed</h2>
    <?php

    // Get cookie viewed_products
    if (is_array($viewedProducts) && count($viewedProducts) > 0) {
        $viewedProducts = implode(",", $viewedProducts);

        $queryViewed = "SELECT * FROM products WHERE id IN ($viewedProducts) ORDER BY RAND() LIMIT " . LIMIT_VIEWED . "";

        $stmt = mysqli_prepare($conn, $queryViewed);
        mysqli_stmt_execute($stmt);
        $resultViewed = mysqli_stmt_get_result($stmt);

        // if (!$stmt) {
        //     die('Lỗi: ' . mysqli_error($conn));
        // }
        // if (!mysqli_stmt_execute($stmt)) {
        //     die('Lỗi: ' . mysqli_stmt_error($stmt));
        // }
        // $resultViewed = mysqli_stmt_get_result($stmt);
        // if (!$resultViewed) {
        //     die('Lỗi: ' . mysqli_error($conn));
        // }


// echo '<pre>';
// var_dump($viewedProducts);
// echo '</pre>';



        while ($productViewed = mysqli_fetch_assoc($resultViewed)) {
    ?>
            <ul>
                <div class="thubmnail-recent">
                    <?php echo '<img src="../../backend/theme/upload/product/' . $productViewed['image'] . ' " class="recent-thumb" alt="">' ?>
                    <h2><a href="single_product.php?id=<?php echo $productViewed['id']  ?>"><?php echo $productViewed['name']; ?></a></h2>
                    <div class="product-sidebar-price">
                        <ins><?php echo '$' . number_format($productViewed['price']); ?></ins>
                        <del><?php echo '$' . number_format($productViewed['old_price']); ?></del>
                    </div>
                </div>
            </ul>
    <?php
        }
    } else {
        echo '';
    }
    ?>
</div>