<?php
    session_start();
    ob_start();
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

    $orderId = $_GET['order_id'] ? $_GET['order_id'] : '';

    // Get query order
    $query = "SELECT * FROM orders WHERE id=$orderId  ORDER BY id DESC";
    $resultOrder = mysqli_query($conn, $query);

    $query = "SELECT * FROM order_items WHERE order_id=$orderId ORDER BY id DESC";
    $resultOrderItems = mysqli_query($conn, $query);
?>

    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                <a href="order_details.php" class="btn btn-small btn-info info-order"><i class="icon-info">Back</i></a>
                    <div class="product-content-right">
                        <div class="woocommerce">
                            <h3 id="order_review_heading" style="text-align:center"># <?php echo $orderId; ?> </h3>
                            <div id="order_review" style="position: relative;">

                                <div class="info-list-order">
                                    <div class="info-order">
                                        <p><b>Customer name:</b></p>
                                        <p><b>Customer phone:</b></p>
                                        <p><b>Customer email:</b></p>
                                        <p><b>Created date:</b></p>
                                        <p><b>Total money:</b></p> </br>
                                    </div>

                                    <div class="list-order-item">
                                        <?php
                                        $rows = mysqli_num_rows($resultOrder);
                                        if ($rows > 0) {
                                            while ($row = mysqli_fetch_array($resultOrder)) {
                                        ?>
                                                <p><?php echo $row['customer_name']; ?></p>
                                                <p><?php echo $row['customer_phone']; ?></p>
                                                <p><?php echo $row['customer_email']; ?></p>
                                                <p><?php echo $row['created_date']; ?></p>
                                                <p>$<?php echo number_format($row['total_money']); ?> </p>

                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <p colspan="8" class="text-custom ">No data.</p>
                                            </tr>
                                        <?php  }
                                        ?>
                                    </div>
                                </div>
                                <table class="shop_table">
                                    <thead>
                                        <tr>
                                            <th class="product-name">Number</th>
                                            <th class="product-name">Product name </th>
                                            <th class="product-total">Product image</th>
                                            <th class="product-total">Product quantity</th>
                                            <th class="product-name">Product price</th>
                                            <th class="product-name">Sub total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        $totalQuantity = 0;
                                        $productPrice = '';
                                        $totalPrice = 0;
                                        $numRows = mysqli_num_rows($resultOrderItems);

                                        if ($numRows > 0) {
                                            $prouctPrice = '';
                                            while ($row = mysqli_fetch_array($resultOrderItems)) {
                                                $i++;
                                                $rowPrice = $row['product_price'];
                                                $rowQuantity = $row['product_quantity'];

                                                $productPrice = $rowQuantity * $rowPrice;
                                                $totalQuantity += $rowQuantity;
                                                $totalPrice += $productPrice;
                                        ?>
                                                <tr class="shipping">
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $row['product_name']; ?> </td>
                                                    <td><?php echo '<img src="../../backend/theme/upload/product/' . $row['product_image'] . ' " width="100px">' ?></td>
                                                    <td><?php echo $row['product_quantity']; ?> </td>
                                                    <td><?php echo number_format($productPrice) ?> </td>
                                                    <td>$<?php echo number_format($rowPrice); ?> </td>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            <tr class="shipping">
                                                <td colspan="3">
                                                    <h4 class="text-list-center"><b>Total</b></h4>
                                                </td>

                                                <td>
                                                    <h4 class="text-list-center"><b><?php echo $totalQuantity; ?></b></h4>
                                                </td>
                                                <td colspan="2">
                                                    <h4 class="text-list-center"><b>$<?php echo number_format($totalPrice); ?></b></h4>
                                                </td>
                                            </tr>
                                        <?php } else {
                                        ?>
                                            <tr>
                                                <td colspan="8" class="text-custom ">No data.</td>
                                            </tr>
                                        <?php  }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
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