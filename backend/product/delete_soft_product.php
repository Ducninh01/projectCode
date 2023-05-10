<?php
    session_start();

    include('../../configs/connect.php');
    include('../common/functions.php');
    include('../../configs/constant.php');

    $id = $_GET['id'];

$record = getParamById($conn, "products", $id, "list_product.php");
deleteRecord($conn, 'products', 'active =' . DELETE_ACTIVE . '', $id, 'Delete product successfully', 'list_product.php');
?>