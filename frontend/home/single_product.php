<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<?php
include "../common/header_js.php";
include('../common/functions.php');
include('../../configs/constant.php');
include('../../configs/connect.php');

$productId = $_GET['id'];
$checkproductId = getParamById($conn, "products", $productId, "./");

// Coolies
$viewedProducts = '';
// Set cookie
if (!isset($_COOKIE['viewed_products'])) {
    setcookie('viewed_products', json_encode(array($productId)), time() +  3600);
} else {
    // Get list ID viewed product
    $viewedProducts = json_decode($_COOKIE['viewed_products'], true);

    // Add ID new product
    if (!in_array($productId, $viewedProducts)) {
        array_push($viewedProducts, $productId);
    }
    setcookie('viewed_products', json_encode($viewedProducts), time() + 3600);
}
// End cookies
?>

<body>
    <?php
    const LIMIT_TOP = 3;

    include "../common/navigation.php";
    include "../common/branding.php";
    include "../common/menu.php";
    include "../common/slider.php";

    ?>

    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <?php include "../common/slidebar.php"; ?>
                </div>

                <!--------Main----------->
                <?php
                $query = "SELECT products.*, categories.name as category_name ,categories.id as categories_id
                FROM products
                INNER JOIN categories ON products.category_id = categories.id
                WHERE products.id = " . $productId . "";

                $resultProducts = mysqli_query($conn, $query);
                $numRows = mysqli_num_rows($resultProducts);
                if ($numRows > 0) {
                    while ($row = mysqli_fetch_array($resultProducts)) {
                ?>
                        <div class="col-md-8">
                            <div class="product-content-right">
                                <div class="product-breadcroumb">
                                    <a href="./">Home</a>
                                    <a href="./category_product.php?category_id=<?php echo $row['categories_id'];  ?>"><?php echo $row['category_name']; ?></a>

                                    <a href=""><?php echo $row['name'] ?></a>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="product-images">
                                            <div class="product-main-img">
                                                <?php echo '<img src="../../backend/theme/upload/product/' . $row['image'] . ' ">' ?>
                                            </div>
                                    <?php
                                }
                            } else {
                                echo '';
                            }
                                    ?>
                                    <?php

                                    $queryGallery = "SELECT *
                                    FROM product_images WHERE product_id = ? AND active=" . ACTIVE . "";

                                    $stmt = mysqli_prepare($conn, $queryGallery);
                                    mysqli_stmt_bind_param($stmt, "i", $productId);
                                    mysqli_stmt_execute($stmt);
                                    $resultGallery = mysqli_stmt_get_result($stmt);

                                    $numRows = mysqli_num_rows($resultGallery);

                                    if ($numRows > 0) {
                                        echo '<div class="product-gallery">';
                                        echo '<div class="related-products-carousel ">';
                                        while ($rowProduct = mysqli_fetch_array($resultGallery)) {
                                            echo '<img src="../../backend/theme/upload/gallery/' . $rowProduct['image_url'] . ' ">';
                                        }

                                    } else {
                                        echo '';
                                    }
                                    echo '</div>';
                                    echo '</div>';
                                    ?>

                                        </div>
                                    </div>

                                    <!--- Name , description , price ---->
                                    <?php
                                    $query = "SELECT products.*, categories.name as category_name ,categories.id as categories_id
                                    FROM products
                                    INNER JOIN categories ON products.category_id = categories.id
                                    WHERE products.id = " . $productId . "";

                                    $resultProducts = mysqli_query($conn, $query);
                                    $numRows = mysqli_num_rows($resultProducts);
                                    if ($numRows > 0) {
                                        while ($row = mysqli_fetch_array($resultProducts)) {
                                    ?>
                                            <div class="col-sm-6">
                                                <div class="product-inner">
                                                    <h2 class="product-name"><?php echo $row['name']; ?></h2>

                                                    <div class="product-inner-price">
                                                        <ins><?php echo '$' . number_format($row['price']); ?></ins>
                                                        <del><?php echo '$' . number_format($row['old_price']); ?></del>
                                                    </div>

                                                  <!---- Form add to cart ---->
                                                    <form action="../cart/save_cart.php" class="cart" method="POST">
                                                        <div class="quantity">
                                                            <input type="number" size="4" class="input-text qty text" title="Qty" value="1" name="quantity" min="1" step="1">

                                                            <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                                            <input type="hidden" name="name" value="<?php echo $row['name'] ?>">
                                                            <input type="hidden" name="image" value="<?php echo $row['image'] ?>">
                                                            <input type="hidden" name="price" value="<?php echo $row['price'] ?>">
                                                        </div>
                                                        <button class="add_to_cart_button" type="submit" name="add_to_cart">Add to cart</button>
                                                    </form>

                                                    <!---- End form add to cart ---->
                                                    <div class="product-inner-category">
                                                        <p>
                                                            Category:<a href="./category_product.php?category_id=<?php echo $row['categories_id'];  ?>">
                                                            <?php echo $row['category_name']; ?>
                                                        </a> Tags: <a href="./tags.php?tags=<?php echo $row['tags'];  ?>"><?php echo $row['tags']; ?></a>
                                                        </p>
                                                    </div>

                                                    <div role="tabpanel">
                                                        <ul class="product-tab" role="tablist">
                                                            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Description</a></li>
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                                                                <h2>Product Description</h2>
                                                                <p><?php echo $row['description']; ?> </p>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo '';
                        }
                        ?>

                        <!----------------Related Products ------------>
                        <div class="related-products-wrapper">
                            <h2 class="related-products-title">Related Products</h2>
                            <div class="related-products-carousel">

                                <?php
                                $query = "SELECT products.*, categories.name as category_name, categories.id as categories_id
                                FROM products
                                INNER JOIN categories ON products.category_id = categories.id
                                WHERE products.id != ? AND products.active = " . ACTIVE . "
                                AND products.category_id = (
                                SELECT category_id
                                FROM products
                                WHERE id = ?
                                )
                                ORDER BY RAND()";

                                $stmt = mysqli_prepare($conn, $query);
                                mysqli_stmt_bind_param($stmt, "ii", $productId, $productId);
                                mysqli_stmt_execute($stmt);
                                $resultSellers = mysqli_stmt_get_result($stmt);

                                $numRows = mysqli_num_rows($resultSellers);
                                if ($numRows > 0) {
                                    while ($row = mysqli_fetch_array($resultSellers)) {
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
        </div>
    </div>

<?php
    include "../common/footer_top.php";
    include "../common/footer_bottom.php";
    include "../common/footer_js.php";
?>
</body>
</html>