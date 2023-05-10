<?php
session_start();

// $id = $_GET['id'];
// $quantity = $_POST['quantity'];
$productCartId = '';
$productCartQuantity = '';

foreach ($_SESSION['save_to_cart'] as $key => $item) {
    $productCartQuantity = $item['quantity'];
    $productCartId = $item['id'];

    echo '<pre>';
    print_r('id'.$productCartId);
    echo '</pre>';


    echo '<pre>';
    print_r("quantity".$productCartQuantity);
    echo '</pre>';

    if(isset($_SESSION['cart'][$productCartId])) {
        $_SESSION['cart'][$productCartId] = $productCartQuantity;
    }

    header("Location: show_cart.php");
    exit();
}






