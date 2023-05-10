<?php
    session_start();

    include('../../configs/connect.php');
    include('../common/functions.php');
    include('../../configs/constant.php');
    $id = $_GET['id'];

getParamById($conn, 'categories', $id, 'list_category.php');
deleteRecord($conn, 'categories', 'active =' . DELETE_ACTIVE . '', $id, 'Delete category successfully','list_category.php');
?>