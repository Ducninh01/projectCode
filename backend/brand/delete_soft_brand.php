<?php
    session_start();

    include('../../configs/connect.php');
    include('../common/functions.php');
    include('../../configs/constant.php');

    $id = $_GET['id'];

getParamById($conn, 'brands', $id, 'list_brand.php');
deleteRecord($conn, 'brands', 'active =' . DELETE_ACTIVE . '', $id, 'Delete brand successfully', 'list_brand.php');
?>