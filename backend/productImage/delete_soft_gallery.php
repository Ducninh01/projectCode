<?php
    session_start();

    include('../../configs/connect.php');
    include('../common/functions.php');

    $id = $_GET['id'];
    $productId ='';

    if (isset($_GET['product_id']) && is_numeric($_GET['product_id']) && $_GET['product_id'] > 0) {
        $productId = (int)$_GET['product_id'];
    }

getParamById($conn, 'product_images', $id, 'list_gallery.php?product_id=' . $_GET['product_id']);
deleteRecord($conn, 'product_images', 'active = 2', $id,'Delete product image successfully', 'list_gallery.php?product_id=' . $_GET['product_id']);
?>