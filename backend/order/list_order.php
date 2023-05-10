<?php
session_start();
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

    // Create numpage, check page
    $numPage = 15;

    $page = '';
    if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) {
        $page = (int) $_GET['page'];
    } else {
        $page = 1;
    }

    $startPage = ($page - 1) * $numPage;

    $query = "SELECT * FROM orders ORDER BY id DESC LIMIT $startPage,$numPage";
    $resultOrder = mysqli_query($conn, $query);
    ?>
    <div class="main">
        <div class="main-inner">
            <div class="container">
                <div class="row">
                    <div class="span12">
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-money"></i>
                                <h3>List order details</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
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
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        $numRows = mysqli_num_rows($resultOrder);
                                        // $totalQuantity = 0;
                                        if ($numRows > 0) {
                                            while ($row = mysqli_fetch_array($resultOrder)) {
                                                $i++;

                                                // $rowQuantity = $row['product_quantity'];
                                                // $totalQuantity += $rowQuantity;

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
                                                        <a href="list_order_details.php?order_id=<?php echo $row['id']; ?>" class="btn btn-small btn-success info-order"><i class="icon-info"> </i></a>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="8" class="text-custom ">No data.</td>
                                            </tr>
                                        <?php  }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!--Pagination-->
                            <?php
                            // Query records display to pagination
                            $querySql = "SELECT * FROM orders ORDER BY id DESC";

                            $resultPage = mysqli_query($conn, $querySql);
                            $totalRecord = mysqli_num_rows($resultPage);

                            $totalPage = ceil($totalRecord / $numPage);
                            ?>

                            <div class="container">
                                <ul class="pagination">
                                    <!-- Previous -->
                                    <?php
                                    if ($page > 1) {
                                    ?>
                                        <a href="list_order.php?page=<?php echo ($page - 1) ?>">Previous</a>
                                    <?php
                                    }
                                    ?>

                                    <!-- Number page -->
                                    <?php
                                    // Check keyword
                                    if ($totalRecord > $numPage) {

                                        for ($i = 1; $i <= $totalPage; $i++) {

                                            if ($page == $i) {
                                                echo '<li><a href="list_order.php?page=' . $i . '" class="active-pagiantion">' . $i . '</a></li>';
                                            } else {
                                                echo '<li><a href="list_order.php?page=' . $i . '">' . $i . '</a></li>';
                                            }
                                        }
                                    } else {
                                        echo '';
                                    }
                                    ?>
                                    <!-- End number page -->

                                    <!-- Next -->
                                    <?php
                                    if ($page < $totalPage) {
                                    ?>
                                        <a href="list_order.php?page=<?php echo ($page + 1) ?>">Next</a>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>

                            <!--End pagination-->
                            </br> </br></br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    include "../common/extra.php";
    include "../common/footer.php";
    include "../common/footer_lib.php";
    ?>
</body>

</html>