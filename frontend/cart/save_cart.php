<?php
session_start();
// Button "add_to_cart"
if (isset($_POST['add_to_cart'])) {

    // Get value in put hidden form single product
    $id = $_POST['id'];
    $name = $_POST['name'];
    $image = $_POST['image'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $items = [
        'id' => $id,
        'name' => $name,
        'image' => $image,
        'price' => $price,
        'quantity' => $quantity
    ];

    if (!isset($_SESSION['save_to_cart'])) {
        $_SESSION['save_to_cart'] = [];
    }

    $exists = false;
    foreach ($_SESSION['save_to_cart'] as $key => $productCart) {
        if ($productCart['id'] == $id) {
            $_SESSION['save_to_cart'][$key]['quantity'] += $quantity;
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        array_push($_SESSION['save_to_cart'], $items);
    }
    header("location: show_cart.php");
}
?>