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

    flashMessage();

    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $activeChange = isset($_POST['active']) ? $_POST['active'] : '';

    if ($activeChange !== '') {
        // Update active in database
        $queryActive = "UPDATE products SET active = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $queryActive);
        mysqli_stmt_bind_param($stmt, "si", $activeChange, $id);
        $result = mysqli_stmt_execute($stmt);
    }

    // Create numpage, check page
    $numPage = 15;

    $page = '';
    if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) {
        $page = (int) $_GET['page'];
    } else {
        $page = 1;
    }

    $startPage = ($page - 1) * $numPage;

    // Search keyword when search
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

    // If no search keyword -> query all records products
    if ($keyword == '') {
        $query = "SELECT products.*, categories.name as category_name , brands.name as brand_name
        FROM products
        INNER JOIN categories ON products.category_id = categories.id
        INNER JOIN brands ON products.brand_id = brands.id
        WHERE (products.active =" . IN_ACTIVE . "  OR products.active = " . ACTIVE . ")
        ORDER BY products.id DESC LIMIT $startPage,$numPage";
    } else {
        // If choose search keyword -> query search key products
        $query = "SELECT products.*, categories.name as category_name , brands.name as brand_name
        FROM products
        INNER JOIN categories ON products.category_id = categories.id
        INNER JOIN brands ON products.brand_id = brands.id
        WHERE (products.active =" . IN_ACTIVE . "  OR products.active = " . ACTIVE . ")
        AND (products.name LIKE '%$keyword%' OR products.price LIKE '%$keyword%' OR products.old_price LIKE '%$keyword%' OR categories.name LIKE '%$keyword%' OR brands.name LIKE '%$keyword%')
        ORDER BY products.id DESC LIMIT $startPage,$numPage";
    }
    $resultQuery = mysqli_query($conn, $query);

?>
    <div class="main">
        <div class="main-inner">
            <div class="container">
                <div class="row">
                    <div class="span12">
                        <form class="navbar-search pull-right" method="GET" action="">
                            <input type="text" name="keyword" value="<?php echo (isset($keyword)) ? $keyword : ''; ?>" placeholder="Search">
                            <button type="submit" class="btn btn-info search">Search</button>
                            <a href="list_product.php" class="btn search">Reset</a>
                        </form>
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-table"></i>
                                <h3>List products</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">Image</th>
                                            <th style="width: 20%;">Name</th>
                                            <th class="table-text" style="width: 8%;">Price</th>
                                            <th class="table-text" style="width: 8%;">Old Price</th>
                                            <th class="table-text" style="width: 7%;">Is Best Sell</th>
                                            <th class="table-text" style="width: 5%;">Is New</th>
                                            <th class="table-text" style="width: 10%;">Category</th>
                                            <th class="table-text" style="width: 7%;">Brand</th>
                                            <th class="table-text" style="width: 7%;">Sort Order</th>
                                            <th class="table-text" style="width: 5%;">Active</th>
                                            <th class="table-text td-actions" style="width: 11%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $numRows = mysqli_num_rows($resultQuery);

                                        if ($numRows > 0) {
                                            while ($row = mysqli_fetch_array($resultQuery)) {
                                        ?>
                                                <tr>
                                                    <td><?php echo '<img src="../theme/upload/product/' . $row['image'] . ' " width="100px">' ?></td>
                                                    <td><?php echo $row['name']; ?></td>
                                                    <td class="table-right"><?php echo '$' . number_format($row['price']); ?></td>
                                                    <td class="table-right"><?php echo '$' . number_format($row['old_price']); ?></td>

                                                    <td class="table-text">
                                                        <?php
                                                        if ($row['is_best_sell'] == ACTIVE) {
                                                            echo '<img src="../theme/img/iconCheck.jpg" alt="" class="iconCheck">';
                                                        } else {
                                                            echo '<img src="../theme/img/iconNG.jpg.jpg" alt=""class="iconCheck">';
                                                        }
                                                        ?>
                                                    </td>

                                                    <td class="table-text">
                                                        <?php
                                                        if ($row['is_new'] == ACTIVE) {
                                                            echo '<img src="../theme/img/iconCheck.jpg" alt="" class="iconCheck">';
                                                        } else {
                                                            echo '<img src="../theme/img/iconNG.jpg.jpg" alt=""class="iconCheck">';
                                                        }
                                                        ?>
                                                    </td>

                                                    <td class=" table-text"><?php echo $row['category_name']; ?>
                                                    </td>
                                                    <td class="table-text"><?php echo $row['brand_name']; ?></td>
                                                    <td class="table-text"><?php echo $row['sort_order']; ?></td>

                                                    <td class="table-text">
                                                        <button class="active-btn
                                                            <?php echo $row['active'] == ACTIVE ? 'btn-success' : 'btn-danger'; ?>" data-id="<?php echo $row['id']; ?>">
                                                            <?php echo $row['active'] == ACTIVE ? 'Active' : 'Inactive'; ?>
                                                        </button>
                                                    </td>

                                                    <td class="td-actions ">
                                                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-small btn-warning mr-2"><i class="icon-edit"> </i></a>
                                                        <a href="../productImage/list_gallery.php?product_id=<?php echo $row['id']; ?>" class="btn btn-small btn-primary mr-2"><i class="icon-picture"> </i></a>
                                                        <a onclick="return confirm('Do you want to delete ?')" href="delete_soft_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-small"><i class="btn-icon-only icon-remove"> </i></a>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="12" class="text-custom ">No data.</td>
                                            </tr>
                                        <?php  }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php
                            // Query records display to pagination
                            $querySql = '';
                            // If no choose search keywords ->get all records
                            if ($keyword == '') {
                                $querySql = "SELECT * FROM products WHERE active !=" . DELETE_ACTIVE . " ";
                            } else {
                                // If no choose key search ->get all records to search keywords
                                $querySql =
                                    "SELECT products.*
                                FROM products
                                INNER JOIN categories ON products.category_id = categories.id
                                INNER JOIN brands ON products.brand_id = brands.id
                                WHERE (products.active =" . IN_ACTIVE . "  OR products.active = " . ACTIVE . ")
                                AND (products.name LIKE '%$keyword%' OR products.price LIKE '%$keyword%' OR products.old_price LIKE '%$keyword%'
                                OR categories.name LIKE '%$keyword%' OR brands.name LIKE '%$keyword%')
                                ORDER BY products.id DESC";
                            }
                            $resultPage = mysqli_query($conn, $querySql);
                            $totalRecord = mysqli_num_rows($resultPage);

                            $totalPage = ceil($totalRecord / $numPage);
                            ?>

                            <div class="container">
                                <ul class="pagination">
                                    <!-- Previous -->
                                    <?php
                                    if ($page > 1) {
                                        if (!empty($keyword)) {
                                    ?>
                                            <a href="list_product.php?keyword=<?php echo $keyword; ?>&page=<?php echo ($page - 1) ?>">Previous</a>
                                        <?php
                                        } else {
                                        ?>
                                            <a href="list_product.php?keyword=<?php echo ''; ?>&page=<?php echo ($page - 1) ?>">Previous</a>
                                    <?php
                                        }
                                    }
                                    ?>

                                    <!-- Number page -->
                                    <?php
                                    // Check keyword
                                    if (!empty($keyword)) {

                                        if ($totalRecord > $numPage) {

                                            for ($i = 1; $i <= $totalPage; $i++) {

                                                if ($page == $i) {
                                                    echo '<li><a href="list_product.php?keyword=' . $keyword . '&page=' . $i . '" class="active-pagiantion">' . $i . '</a></li>';
                                                } else {
                                                    echo '<li><a href="list_product.php?keyword=' . '' . '&page=' . $i . '">' . $i . '</a></li>';
                                                }
                                            }
                                        } else {
                                            echo '';
                                        }
                                    } else {
                                        // No keywords
                                        if ($totalRecord > $numPage) {

                                            for ($i = 1; $i <= $totalPage; $i++) {

                                                if ($page == $i) {
                                                    echo '<li><a href="list_product.php?page=' . $i . '" class="active-pagiantion">' . $i . '</a></li>';
                                                } else {
                                                    echo '<li><a href="list_product.php?page=' . $i . '">' . $i . '</a></li>';
                                                }
                                            }
                                        } else {
                                            echo '';
                                        }
                                    }
                                    ?>
                                    <!-- End number page -->

                                    <!-- Next -->
                                    <?php
                                    if ($page < $totalPage) {
                                        if (!empty($keyword)) {
                                    ?>
                                            <a href="list_product.php?keyword=<?php echo $keyword; ?>&page=<?php echo ($page + 1) ?>">Next</a>
                                        <?php
                                        } else {
                                        ?>
                                            <a href="list_product.php?keyword=<?php echo ''; ?>&page=<?php echo ($page + 1) ?>">Next</a>
                                    <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>

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
<script>
    // Change button active and inactive status
    $(document).on('click', '.active-btn', function() {
        var id = $(this).data('id');
        var active = $(this).hasClass('btn-success') ? 0 : 1;
        var self = $(this);

        $.ajax({
            url: 'list_product.php',
            type: 'POST',
            data: {
                id: id,
                active: active
            },
            success: function(response) {
                self.toggleClass('btn-success btn-danger');
                self.text(active == 1 ? 'Active' : 'Inactive');
            }
        });
    });
</script>
</html>