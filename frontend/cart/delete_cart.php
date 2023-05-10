<?php
session_start();
// Delete
foreach ($_SESSION['save_to_cart'] as $key => $productCartDelete) {
    $id = $_GET['id'];
    if ($productCartDelete['id'] == $id) {
        unset($_SESSION['save_to_cart'][$key]);
    }
}
header("location: show_cart.php");
?>