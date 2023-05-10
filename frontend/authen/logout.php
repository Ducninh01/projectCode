<?php
session_start();

if (isset($_SESSION['name_login'])) {
    unset($_SESSION['name_login']);
    header("location: ../home/");
    // exit;
}

if (isset($_SESSION['id'])) {
    unset($_SESSION['id']);
    header("location: ../home/");
    // exit;
}

else {
    header("location: ../home/");
}
?>