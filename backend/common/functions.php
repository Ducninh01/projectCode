<?php

function flashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_message']['type'];
        $message = $_SESSION['flash_message']['message'];

        echo '<script>';
        echo "Swal.fire({
                position: 'top-end',
                icon: '$type',
                title: '$message',
                showConfirmButton: false,
                timer: 1500
            })";
        echo '</script>';

        unset($_SESSION['flash_message']);
    }
}

function getCheckIdUrl($location, $getIdUrl)
{
    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
        (int)$getIdUrl;
    } else {
        header("location: $location");
        exit;
    }
}

function getParamById($conn, $table, $id, $location)
{
    $query = "SELECT * FROM $table  WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        header("location: $location");
        exit;
    }
}

function checkProductIdUrl($conn, $table, $id, $location)
{
    $query = "SELECT * FROM $table  WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        header("location: $location");
        exit;
    }
}

function checkOrderIdUrl($conn, $table, $id, $location)
{
    $query = "SELECT * FROM $table  WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        header("location: $location");
        exit;
    }
}

function checkToken($conn, $email, $token)
{
    $query = "SELECT * FROM users  WHERE email = ? AND token = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $token);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        header("location: ../error/error.php");
        exit;
    }
}


function queryAll($table, $condition, $orderBy, $sortBy)
{
    return "SELECT * FROM $table WHERE $condition ORDER BY $orderBy $sortBy";
}

function deleteRecord($conn, $table, $column, $id, $typeMessage, $location)
{
    $query = "UPDATE $table SET $column  WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $deleteSoft = mysqli_stmt_execute($stmt);

    if ($deleteSoft) {
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => $typeMessage,
        ];

        header("location: $location");
    }
}

// function check validate

function checkRequire($string)
{
    return $string != '' ? true : false;
}

function checkLength($string)
{
    return $string >= 3 ? true : false;
}

function checkLengthPassword($string)
{
    return $string >= 5 ? true : false;
}

function checkInvalidNumber($string)
{
    return (!preg_match('/^\d+$/', $string));
}

function checkInvalidLink($string)
{
    return (!filter_var($string, FILTER_VALIDATE_URL));
}

function checkInvalidEmail($string)
{
    return (!preg_match('/^[\w.-]+@([\w-]+\.)+[\w-]{2,4}$/', $string));
}

function checkInvalidUserName($string)
{
    return (!preg_match('/^[a-zA-Z0-9]+$/', $string));
}

function checkInvalidMobilePhone($string)
{
    return (!preg_match('/^[0-9]{10}+$/', $string));
}

/* check pagination */
// function printPaginationLink($pageNumber, $active = false) {
//     $class = ($active ? 'active' : '');
//     return "<li class='{$class}'><a href='top_seller.php?page={$pageNumber}' class='{$class}'>{$pageNumber}</a></li>";
// }