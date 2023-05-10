<?php
    session_start();
    ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<?php
    include "../common/header_lib.php";
?>
<body>
<?php
    include('../../configs/constant.php');
    include('../../configs/connect.php');
    include('../common/functions.php');
    include('../common/navbar_top.php');
    include('../common/sub_navbar.php');

    $orderId = $_GET['order_id'] ? $_GET['order_id'] : '';
    // Check order id url exists
    $record = checkOrderIdUrl($conn, "order_items", $orderId, "list_order.php");

    $query = "SELECT * FROM order_items WHERE order_id=$orderId ORDER BY id DESC";
    $resultOrderItems = mysqli_query($conn, $query);

    // Get query order
    $query = "SELECT * FROM orders WHERE id=$orderId  ORDER BY id DESC";
    $resultOrder = mysqli_query($conn, $query);

?>
    <div class="main">
        <div class="main-inner">
            <div class="container">
                <div class="row">
                    <div class="span12">
                        <a href="../order/list_order.php" class="back-order-details btn btn-primary">Back</a>
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-money"></i>
                                <h3>List order details</h3>
                            </div>
                            <div class="widget-content">

                                <div class="info-order span2">
                                    <p><b>Customer name:</b></p>
                                    <p><b>Customer phone:</b></p>
                                    <p><b>Customer email:</b></p>
                                    <p><b>Created date:</b></p>
                                    <p><b>Total money:</b></p> </br>
                                </div>

                                <div class="span8 list-order-item">
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

                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="product-name">No </th>
                                            <th class="product-total">Product image</th>
                                            <th class="product-name">Product name </th>
                                            <th class="product-total" style="text-align:center;">Product quantity</th>
                                            <th class="product-name" style="text-align:center;">Product price</th>
                                            <th class="product-name" style="text-align:center;">Sub total</th>
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
                                                    <td><?php echo '<img src="../theme/upload/product/' . $row['product_image'] . ' " width="100px">' ?></td>
                                                    <td><?php echo $row['product_name']; ?> </td>
                                                    <td style="text-align:center;"><?php echo $rowQuantity; ?> </td>
                                                    <td style="text-align:center;">$<?php echo number_format($productPrice); ?> </td>
                                                    <td style="text-align:center;">$<?php echo number_format($rowPrice); ?> </td>
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
                                                <td colspan="8" class="text-custom">No data.</td>
                                            </tr>
                                        <?php  }
                                        ?>
                                    </tbody>

                                </table>
                            </div>
                            </br> </br></br> </br> </br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    ob_end_flush();
    include "../common/extra.php";
    include "../common/footer.php";
    include "../common/footer_lib.php";
?>
</body>
</html>