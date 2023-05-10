<?php
    session_start();

    include('../../configs/connect.php');
    include('../common/functions.php');
    include('../../configs/constant.php');

    $id = $_GET['id'];

$record = getParamById($conn, "customers", $id, "list_customer.php");
deleteRecord($conn, 'customers', 'active =' . DELETE_ACTIVE . '', $id, 'Delete customer successfully', 'list_customer.php');
?>