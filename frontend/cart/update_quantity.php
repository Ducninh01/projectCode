<?php
session_start();

$id = $_GET['id'];
$type = $_GET['type'];

foreach ($_SESSION['save_to_cart'] as $key => $productQuantity) {
    if ($productQuantity['id'] == $id) {
        //Set type = incre (+)
        if ($type == 'incre') {
            $_SESSION['save_to_cart'][$key]['quantity']++;

            //Set type = decre (-)
        } else {

            if ($type == 'decre' && $_SESSION['save_to_cart'][$key]['quantity'] > 1) {
                $_SESSION['save_to_cart'][$key]['quantity']--;
            } else {
                // quantity < 1  - delete record save cart
                unset($_SESSION['save_to_cart'][$key]);
            }
        }
    }
}
header("Location: show_cart.php");
?>