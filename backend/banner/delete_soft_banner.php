<?php
    session_start();

    include('../../configs/connect.php');
    include('../common/functions.php');
    include('../../configs/constant.php');

    $id = $_GET['id'];

getParamById($conn, 'banners', $id, 'list_banner.php');
deleteRecord($conn, 'banners', 'active =' . DELETE_ACTIVE . '', $id, 'Delete banner successfully', 'list_banner.php');
?>