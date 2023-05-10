<?php
$conn = mysqli_connect("localhost", "root", "", "sell_phones");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
