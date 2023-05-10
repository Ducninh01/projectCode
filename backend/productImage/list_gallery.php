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
    flashMessage();

    // Check product_id url
    $productId = isset($_GET['product_id']) ? $_GET['product_id'] : '';
    $record = getParamById($conn, "products", $productId, "../product/list_product.php");

    // Check product_images , get id and active => update
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $activeChange = isset($_POST['active']) ? $_POST['active'] : '';

    // Update active in database
    $queryActive = "UPDATE product_images SET active = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $queryActive);
    mysqli_stmt_bind_param($stmt, "si", $activeChange, $id);
    $result = mysqli_stmt_execute($stmt);

    // Query all records product_images
    $query = "SELECT * FROM product_images WHERE product_id = ? AND (active = " . IN_ACTIVE . " OR active = " . ACTIVE . ")
    ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);
    $resultProImages = mysqli_stmt_get_result($stmt);
?>
    <div class="main">
        <div class="main-inner">
            <div class="container">
                <div class="row">
                    <div class="span12">
                        <div class="add_productImage">
                            <a href="../product/list_product.php" class="btn btn-primary ">Back to list products</a>
                            <a href="add_gallery.php?product_id=<?php echo $productId; ?>" class="btn btn-primary ">Add product images</a>
                        </div>
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-picture"></i>
                                <h3>List product images </h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:3%;">Image</th>
                                            <th class="table-text" style="width:5%;">Sort order</th>
                                            <th class="table-text" style="width:5%;">Active</th>
                                            <th style="width:35%;"></th>
                                            <th class="td-actions table-text" style="width:8%;">Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        $numRows = mysqli_num_rows($resultProImages);
                                        if ($numRows > 0) {
                                            while ($row = mysqli_fetch_array($resultProImages)) {
                                                $i++;
                                        ?>
                                                <tr>
                                                    <td><?php echo '<img src="../theme/upload/gallery/' . $row['image_url'] . ' " width="100px">' ?></td>
                                                    <td class="table-text"><?php echo $row['sort_order']; ?></td>

                                                    <td class="table-text">
                                                        <button class="active-btn <?php echo $row['active'] == 1 ? 'btn-success' : 'btn-danger'; ?>" data-id="<?php echo $row['id']; ?>" data-product-id="<?php echo $productId; ?>">
                                                            <?php echo $row['active'] == 1 ? 'Active' : 'Inactive'; ?>
                                                        </button>
                                                    </td>

                                                    <td style="width:35%"></td>

                                                    <td class="td-actions table-text">
                                                        <a href="./edit_gallery.php?id=<?php echo $row['id']; ?>&product_id=<?php echo $_GET['product_id']; ?>" class="btn btn-small btn-warning mr-2"><i class="icon-edit"> </i></a>
                                                        <a onclick="return confirm('Do you want to delete ?')" href="./delete_soft_gallery.php?id=<?php echo $row['id']; ?>&product_id=<?php echo $_GET['product_id']; ?>" class="btn btn-danger btn-small"><i class="btn-icon-only icon-remove"> </i></a>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
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
    ob_end_flush();
    include "../common/extra.php";
    include "../common/footer.php";
    include "../common/footer_lib.php";
?>
</body>
<script>
    // Change button active and inactive status
    $(document).on('click', '.active-btn', function() {
        let self = $(this);
        let id = self.data('id');
        let productId = self.data('product-id');
        let active = self.hasClass('btn-success') ? 0 : 1;
        const url = 'list_gallery.php?product_id=' + productId;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                id: id,
                active: active,
                productId: productId
            },
            success: function(response) {
                self.toggleClass('btn-success btn-danger');
                self.text(active == 1 ? 'Active' : 'Inactive');
            }
        });
    });
</script>
</html>