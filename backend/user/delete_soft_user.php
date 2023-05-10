<?php
    session_start();

    include('../../configs/connect.php');
    include('../common/functions.php');
    include('../../configs/constant.php');

    $id = $_GET['id'];

$record = getParamById($conn, "users", $id, "list_user.php");
deleteRecord($conn, 'users', 'active =' . DELETE_ACTIVE . '', $id, 'Delete user successfully', 'list_user.php');
?>