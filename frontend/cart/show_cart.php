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
?>

<body>
    <?php

    include "../common/navigation.php";
    include "../common/branding.php";
    include "../common/menu.php";
    include "../common/slider.php";

    const LIMIT_RELATE = 2;
    $productCartId = '';
    $productCartQuantity = '';

        if(isset($_SESSION['save_to_cart'])){

            foreach ($_SESSION['save_to_cart'] as $key => $item) {
                $productCartQuantity = $item['quantity'];
                $productCartId = $item['id'];
            }

        }else{
            echo '';
        }

    ?>
    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <?php include "../common/slidebar.php"; ?>
                </div>

                <!---------Main Show cart product--------->
                <div class="col-md-8">
                    <div class="product-content-right">
                        <?php
                        if (isset($_SESSION['save_to_cart']) && count($_SESSION['save_to_cart']) > 0) {
                        ?>
                            <div class="woocommerce">
                            <form method="post" action="update_cart_quantity.php">

                                    <table cellspacing="0" class="shop_table cart">
                                        <thead>
                                            <tr>
                                                <th class="product-remove">&nbsp;</th>
                                                <th class="product-thumbnail">&nbsp;</th>
                                                <th class="product-name">Product</th>
                                                <th class="product-price">Price</th>
                                                <th class="product-quantity">Quantity</th>
                                                <th class="product-subtotal">Total</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $count = 0;
                                            $orderTotal=0;

                                            foreach ($_SESSION['save_to_cart'] as $key => $item) {
                                                $productCartQuantity = $item['quantity'];
                                                $orderTotal+=$productCartQuantity;
                                            ?>
                                                <tr class="cart_item">
                                                    <td class="product-remove">
                                                        <a title="Remove this item" class="remove" href="delete_cart.php?id= <?php echo $item['id']; ?>">×</a>
                                                    </td>

                                                    <td class="product-thumbnail">
                                                        <?php echo '<img src="../../backend/theme/upload/product/' . $item['image'] . ' ">'; ?>
                                                        <!-- <a href="single-product.html"><img width="145" height="145" alt="poster_1_up" class="shop_thumbnail" src="img/product-thumb-2.jpg"></a> -->
                                                    </td>

                                                    <td class="product-name">
                                                        <a href="single-product.html"><?php echo $item['name']; ?></a>
                                                    </td>

                                                    <td class="product-price">
                                                        <span class="amount">$<?php echo $item['price']; ?></span>
                                                    </td>

                                                    <td class="product-quantity">
                                                        <div class="quantity buttons_added">
                                                            <a class="minus" href="update_quantity.php?id=<?php echo $item['id']; ?>&type=decre"> - </a>

                                                            <input type="number" size="4" class="input-text qty text" title="Qty" value="<?php echo $item['quantity']; ?>" name="quantity" id="quantity" min="1" step="1">
                                                            <a class="minus" href="update_quantity.php?id=<?php echo $item['id']; ?>&type=incre"> + </a>
                                                        </div>
                                                    </td>

                                                    <td class="product-subtotal">
                                                        <?php
                                                        $quantity = $item['quantity'];
                                                        $price = $item['price'];
                                                        $total = $quantity * $price;

                                                        // total count price
                                                        $count +=  $total;
                                                        ?>
                                                        <span class="amount">
                                                            <?php echo '$' .number_format($total)
                                                            ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php }
                                            ?>
                                            <tr>
                                                <td class="actions" colspan="6">
                                                    
                                                    <a href=" ../checkout/checkout.php" class="btn btn-primary">Checkout</a>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </form>

                                <div class="cart-collaterals">
                                    <!-----------Related Products ------------->
                                    <div class="cross-sells">
                                        <h2>Related Products</h2>
                                        <ul class="products">

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
                                            ORDER BY RAND()  LIMIT " . LIMIT_RELATE . " ";

                                            $stmt = mysqli_prepare($conn, $query);
                                            mysqli_stmt_bind_param($stmt, "ii", $productCartId, $productCartId);
                                            mysqli_stmt_execute($stmt);
                                            $resultSellers = mysqli_stmt_get_result($stmt);

                                            $numRows = mysqli_num_rows($resultSellers);
                                            if ($numRows > 0) {
                                                while ($row = mysqli_fetch_array($resultSellers)) {
                                            ?>
                                                    <li class="product">
                                                        <div class="single-product">
                                                            <div class="product-f-image">
                                                                <?php echo '<img src="../../backend/theme/upload/product/' . $row['image'] . ' ">' ?>
                                                                <div class="product-hover">

                                                                    
                                                                    <a href="../home/single_product.php?id=<?php echo $row['id']  ?>" class="view-details-link"><i class="fa fa-link"></i> See details</a>
                                                                </div>
                                                            </div>

                                                            <h3><a href="../home/single_product.php?id=<?php echo $row['id']  ?>"><?php echo $row['name']; ?></a></h3>

                                                            <div class="product-carousel-price">
                                                                <ins><?php echo '$' . number_format($row['price']); ?></ins>
                                                                <del><?php echo '$' . number_format($row['old_price']); ?></del>
                                                            </div>
                                                        </div> <br>
                                                    </li>

                                            <?php
                                                }
                                            } else {
                                                echo '';
                                            }
                                            ?>

                                        </ul>
                                    </div>

                                    <!-----------Related Products ------------->
                                    <div class="cart_totals ">
                                        <h2>Cart Totals</h2>
                                        <table cellspacing="0">
                                            <tbody>
                                                <tr class="cart-subtotal">
                                                    <th>Cart Subtotal</th>
                                                    <td><span class="amount"><?php echo '$' . number_format($count); ?></span></td>
                                                </tr>

                                                <tr class="shipping">
                                                    <th>Shipping and Handling</th>
                                                    <td>Free Shipping</td>
                                                </tr>

                                                <tr class="order-total">
                                                    <th>Order Total</th>
                                                    <td><strong>
                                                            <span class="amount">
                                                                <?php
                                                                if (isset($_SESSION['save_to_cart']) && $_SESSION['save_to_cart'] > 0) {
                                                                    echo $orderTotal;

                                                                } else {
                                                                    echo 0;
                                                                }
                                                                ?>
                                                            </span>
                                                        </strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php  } else { ?>
                            <h1 class="cart-empty">Your shopping cart is empty</h1>
                        <?php  } ?>
                    </div>
                </div>
                <!--------End main---------->
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