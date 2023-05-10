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

    $sessionEmailLogin = isset($_SESSION['email']) ?  $_SESSION['email'] : '';

    $query = "SELECT * FROM orders WHERE customer_email = ? ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $sessionEmailLogin);
    mysqli_stmt_execute($stmt);
    $resultOrder = mysqli_stmt_get_result($stmt);
?>

    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">

                <div class="col-md-12">
                    <div class="product-content-right">
                        <div class="woocommerce">
                            <h3 id="order_review_heading" style="text-align:center; margin-bottom:20px;">Your order details</h3>
                            <div id="order_review" style="position: relative;">
                                <table class="shop_table">
                                    <thead>
                                        <tr>
                                            <th class="product-name">No</th>
                                            <th class="product-total">Date</th>
                                            <th class="product-name">Name</th>
                                            <th class="product-total">Phone</th>
                                            <th class="product-name">Email</th>
                                            <th class="product-name">Total products</th>
                                            <th class="product-total">Total money</th>
                                            <th class="product-total">Status</th>
                                            <th class="product-total">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <?php
                                        $i = 0;
                                        $numRows = mysqli_num_rows($resultOrder);

                                        if ($numRows > 0) {
                                            while ($row = mysqli_fetch_array($resultOrder)) {
                                                $i++;
                                        ?>
                                            <tr class="shipping">
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $row['created_date']; ?> </td>
                                                <td><?php echo $row['customer_name']; ?> </td>
                                                <td><?php echo $row['customer_phone']; ?> </td>
                                                <td><?php echo $row['customer_email']; ?> </td>
                                                <td><?php echo $row['total_products']; ?> </td>
                                                <td>$<?php echo number_format($row['total_money']); ?> </td>
                                                <td><?php echo $row['status'] == 0 ? 'Unpaid' : 'Paid'; ?> </td>
                                                <td class="td-actions">
                                                <a href="list_order_details.php?order_id=<?php echo $row['id'];  ?>" class="btn btn-small btn-success info-order"><i class="icon-info">Details</i></a>
                                                </td>
                                            </tr>
                                        <?php }
                                        } else {
                                            echo '<h1 class="cart-empty">Please select a product to payments</h1>';
                                        } ?>
                                    </tfoot>
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