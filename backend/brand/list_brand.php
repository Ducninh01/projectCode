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

    // Check id and active status
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $activeChange = isset($_POST['active']) ? $_POST['active'] : '';

    // Update active in database
    $queryActive = "UPDATE brands SET active = ? WHERE id = ? ";
    $stmt = mysqli_prepare($conn, $queryActive);
    mysqli_stmt_bind_param($stmt, "si", $activeChange, $id);
    $result = mysqli_stmt_execute($stmt);

    // Query all brands show data in the table
    $query = queryAll('brands', '(active =' . IN_ACTIVE . ' OR active = ' . ACTIVE . ')', 'id', 'DESC');
    $resultBrands = mysqli_query($conn, $query);
?>
    <div class="main">
        <div class="main-inner">
            <div class="container">
                <div class="row">
                    <div class="span12">
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-th-list"></i>
                                <h3>List brands</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:10%;">Image</th>
                                            <th style="width:20%;">Name</th>
                                            <th style="width:20%;">Link</th>
                                            <th class="table-text" style="width:8%;">Sort order</th>
                                            <th class="table-text" style="width:8%;">Active</th>
                                            <th style="width:5%;"></th>
                                            <th class="table-text td-actions" style="width:11%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $numRows = mysqli_num_rows($resultBrands);
                                        if ($numRows > 0) {
                                            while ($row = mysqli_fetch_array($resultBrands)) {
                                        ?>
                                            <tr>
                                                <td><?php echo '<img src="../theme/upload/brand/' . $row['image_url'] . ' " width="100px">' ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['link']; ?></td>
                                                <td class="table-text"><?php echo $row['sort_order']; ?></td>

                                                <td class="table-text">
                                                    <button class="active-btn
                                                    <?php echo $row['active'] == 1 ? 'btn-success' : 'btn-danger'; ?>" data-id="<?php echo $row['id']; ?>">
                                                    <?php echo $row['active'] == 1 ? 'Active' : 'Inactive'; ?>
                                                    </button>
                                                </td>
                                            <td style="width:5%;"></td>


                                                <td class="td-actions ">
                                                    <a href="edit_brand.php?id=<?php echo $row['id']; ?>" class="btn btn-table-edit btn-small btn-warning" style="margin-left:30px;"><i class="icon-edit"> </i></a>
                                                    <a onclick="return confirm('Do you want to delete ?')" href="delete_soft_brand.php?id=<?php echo $row['id']; ?>" class="btn  btn-table-delete btn-danger btn-small"><i class="btn-icon-only icon-remove"> </i></a>
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
            url: 'list_brand.php',
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