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

    // Update active in database
    $queryActive = "UPDATE customers SET active = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $queryActive);
    mysqli_stmt_bind_param($stmt, "si", $activeChange, $id);
    $result = mysqli_stmt_execute($stmt);

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
        $query = "SELECT *
        FROM customers
        WHERE (active =" . IN_ACTIVE . "  OR active = " . ACTIVE . ")
        ORDER BY id DESC LIMIT $startPage,$numPage";
    } else {
        // If choose search keyword -> query search key products
        $query = "SELECT *
        FROM customers
        WHERE (active =" . IN_ACTIVE . "  OR active = " . ACTIVE . ")
        AND (name LIKE '%$keyword%' OR email LIKE '%$keyword%' OR phone LIKE '%$keyword%' OR active LIKE '%$keyword%')
        ORDER BY id DESC LIMIT $startPage,$numPage";
    }
    $resultCustomer = mysqli_query($conn, $query);
?>

    <div class="main">
        <div class="main-inner">
            <div class="container">
                <div class="row">
                    <div class="span12">
                        <form class="navbar-search pull-right" method="GET" action="">
                            <input type="text" name="keyword" value="<?php echo (isset($keyword)) ? $keyword : ''; ?>" placeholder="Search">
                            <button type="submit" class="btn btn-info search">Search</button>
                            <a href="list_customer.php" class="btn search">Reset</a>
                        </form>
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-group"></i>
                                <h3>List customers</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:18%">Name</th>
                                            <th style="width:20%">Email</th>
                                            <th style="width:10%">Phone</th>
                                            <th class="table-text" style="width:10%">Active</th>
                                            <th style="width:25%"></th>
                                            <th class="td-actions table-text" style="width:13%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $numRows = mysqli_num_rows($resultCustomer);

                                        if ($numRows > 0) {
                                            while ($row = mysqli_fetch_array($resultCustomer)) {
                                        ?>
                                                <tr>
                                                    <td><?php echo $row['name']; ?></td>
                                                    <td><?php echo $row['email']; ?></td>
                                                    <td><?php echo $row['phone']; ?></td>

                                                    <td class="table-text">
                                                        <button class="active-btn
                                                        <?php echo $row['active'] == 1 ? 'btn-success' : 'btn-danger'; ?>" data-id="<?php echo $row['id']; ?>">
                                                            <?php echo $row['active'] == 1 ? 'Active' : 'Inactive'; ?>
                                                        </button>
                                                    </td>

                                                    <td style="width:20%"></td>

                                                    <td class="td-actions">
                                                        <a href="edit_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-table-edit btn-small btn-warning" style="margin-left: 30px;"><i class="icon-edit"> </i></a>
                                                        <a onclick="return confirm('Do you want to delete ?')" href="delete_soft_customer.php?id=<?php echo $row['id']; ?>" class="btn  btn-table-delete btn-danger btn-small"><i class="btn-icon-only icon-remove"> </i></a>
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
                                $querySql = "SELECT * FROM customers WHERE (active =" . IN_ACTIVE . "  OR active = " . ACTIVE . ") ";
                            } else {
                                // If no choose key search ->get all records to search keywords
                                $querySql = "SELECT *
                                FROM customers
                                WHERE (active =" . IN_ACTIVE . "  OR active = " . ACTIVE . ")
                                AND (name LIKE '%$keyword%' OR email LIKE '%$keyword%' OR phone LIKE '%$keyword%' OR active LIKE '%$keyword%')
                                ORDER BY id DESC";
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
                                            <a href="list_customer.php?keyword=<?php echo $keyword; ?>&page=<?php echo ($page - 1) ?>">Previous</a>
                                        <?php
                                        } else {
                                        ?>
                                            <a href="list_customer.php?keyword=<?php echo ''; ?>&page=<?php echo ($page - 1) ?>">Previous</a>
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
                                                    echo '<li><a href="list_customer.php?keyword=' . $keyword . '&page=' . $i . '" class="active-pagiantion">' . $i . '</a></li>';
                                                } else {
                                                    echo '<li><a href="list_customer.php?keyword=' . '' . '&page=' . $i . '">' . $i . '</a></li>';
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
                                                    echo '<li><a href="list_customer.php?page=' . $i . '" class="active-pagiantion">' . $i . '</a></li>';
                                                } else {
                                                    echo '<li><a href="list_customer.php?page=' . $i . '">' . $i . '</a></li>';
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
                                            <a href="list_customer.php?keyword=<?php echo $keyword; ?>&page=<?php echo ($page + 1) ?>">Next</a>
                                        <?php
                                        } else {
                                        ?>
                                            <a href="list_customer.php?keyword=<?php echo ''; ?>&page=<?php echo ($page + 1) ?>">Next</a>
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
            url: 'list_customer.php',
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