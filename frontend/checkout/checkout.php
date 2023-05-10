<?php
session_start();
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<?php
include "../common/header_js.php";
include('../common/functions.php');
include('../common/send_mail_checkout.php');
include('../../configs/constant.php');
include('../../configs/connect.php');
?>

<body>
    <?php
    include "../common/navigation.php";
    include "../common/branding.php";
    include "../common/menu.php";
    include "../common/slider.php";

    const LIMIT_RELATE = 2;
    $productCartId = '';
    $sessionId = isset($_SESSION['id']) ?  $_SESSION['id'] : '';
    $name_login = isset($_SESSION['name_login']) ?  $_SESSION['name_login'] : '';

    $currentDatetime = date("Y-m-d H:i:s");

    $query = "SELECT * FROM customers  WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $sessionId);
    mysqli_stmt_execute($stmt);
    $resultCustomer = mysqli_stmt_get_result($stmt);

    $numRows = mysqli_num_rows($resultCustomer);

    $count = 0;
    $countMoney = 0;
    $countProduct = 0;
    $nameProduct = '';
    $productQuantity = '';
    $sessionSavecard = isset($_SESSION['save_to_cart']) ? $_SESSION['save_to_cart'] : '';

    if (!empty($sessionSavecard)) {

        foreach ($sessionSavecard as $key => $item) {
            $productPrice = $item['price'];
            $productQuantity = $item['quantity'];
            $nameProduct = $item['name'];

            $total = $productQuantity * $productPrice;

            // Total count price
            $countMoney +=  $total;
            $countProduct += $productQuantity;
        }
    } else {
        echo '';
    }

    $errors = [];
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        //Get value form.
        $name = isset($_POST["customer_name"]) ? $_POST["customer_name"] : '' ;
        $email = isset($_POST["customer_email"]) ? $_POST["customer_email"] :'';
        $phone = isset($_POST["customer_phone"]) ? $_POST["customer_phone"] :'';
        $address = isset($_POST["address"]) ? $_POST["address"] :'';

        // Check validate form

        if (!checkRequire(trim($name))) {
            $errors['customer_name'] = 'Please type name.';
        } else {
            if (!checkLength(strlen($name))) {
                $errors["customer_name"] = 'Name must be more than 3 characters.';
            }
        }

        if (!checkRequire(trim($email))) {
            $errors["customer_email"] = 'Please type email.';
        } else {
            if (checkInvalidEmail($email)) {
                $errors["customer_email"] = 'Invalid email format.';
            }
        }

        if (!checkRequire(trim($phone))) {
            $errors['customer_phone'] = 'Please type phone.';
        } else {
            if (checkInvalidMobilePhone($phone)) {
                $errors["customer_phone"] = 'Invalid phone format.';
            }
        }

        if (!checkRequire(trim($address))) {
            $errors['address'] = 'Please type address.';
        }

        if (!isset($_POST["status"])) {
            $errors['status'] = 'Please select a payment .';
        } else {
            $status = $_POST["status"];
        }

        // if empty error message

        if (empty($errors)) {
            $stmtInsert = mysqli_prepare($conn, "INSERT INTO orders (`customer_name`,`customer_phone`,`customer_email`,`total_money`, `total_products`,`created_date`,`status`) VALUES (?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmtInsert, "sssssss", $name, $phone, $email, $countMoney, $countProduct, $currentDatetime, $status);
            $resultInsert = mysqli_stmt_execute($stmtInsert);

            if ($resultInsert) {
                $lastId = mysqli_insert_id($conn);
                foreach ($_SESSION['save_to_cart'] as $product) {
                    $productId = $product['id'];
                    $productName = $product['name'];
                    $productImage = $product['image'];

                    $productPrice = $product['price'];
                    $productQuantity = $product['quantity'];

                    $totalProductPrice = $productPrice * $productQuantity;

                    $stmtInsert = mysqli_prepare($conn, "INSERT INTO order_items (`order_id`,`product_id`,`product_name`,`product_image`, `product_price`,`product_quantity`) VALUES (?,?,?,?,?,?)");
                    mysqli_stmt_bind_param($stmtInsert, "ssssss", $lastId, $productId, $productName, $productImage, $productPrice, $productQuantity);
                    $resultInsert = mysqli_stmt_execute($stmtInsert);
                }

                // Send mail notification checkout success
                sendMailCheckout($name, $email, $phone, $address, $currentDatetime, $sessionSavecard);
                header("Location: checkout_success.php");
                // Checkout success unset product items cart
                unset($_SESSION['save_to_cart']);
                exit;
            } else {
                echo 'error';
            }
        }
    }
    ?>

    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <?php include "../common/slidebar.php"; ?>
                </div>
                <!---------Main--------->
                <div class="col-md-8">
                    <div class="product-content-right">
                        <?php
                        if (isset($_SESSION['save_to_cart']) && count($_SESSION['save_to_cart']) > 0) {
                        ?>
                            <div class="woocommerce">

                                <form action="" class="checkout" method="post" name="checkout">
                                    <div id="customer_details" class="col2-set">
                                        <div class="col-1">
                                            <?php
                                            if ($numRows > 0) {
                                                while ($row = mysqli_fetch_array($resultCustomer)) {
                                            ?>
                                                    <div class="woocommerce-billing-fields">
                                                        <h3>Billing Details</h3>

                                                        <p id="billing_customer_name_field" class="form-row form-row-first validate-required">
                                                            <label class="" for="customer_name">Name <span class="color-red">*</span>
                                                            </label>
                                                            <input type="text" class="span6" id="customer_name" name="customer_name" value="<?php echo $row['name']; ?>">
                                                        <p class="error" style="color:red; float:left"><?php echo isset($errors['customer_name']) ? $errors['customer_name'] : ''; ?> </p>
                                                        <br>
                                                        </p>

                                                        <p id="billing_customer_email_field" class="form-row form-row-first validate-required">
                                                            <label class="" for="customer_email">Email <span class="color-red">*</span>
                                                            </label>
                                                            <input type="text" class="span6" id="customer_email" name="customer_email" value="<?php echo $row['email']; ?>">
                                                        <p class="error" style="color:red; float:left"><?php echo isset($errors['customer_email']) ? $errors['customer_email'] : ''; ?> </p>
                                                        <br>
                                                        </p>

                                                        <p id="billing_customer_phone_field" class="form-row form-row-last validate-required">
                                                            <label class="" for="customer_phone">Phone <span class="color-red">*</span>
                                                            </label>
                                                            <input type="text" class="span6" id="customer_phone" name="customer_phone" value="<?php echo $row['phone']; ?>">

                                                        <p class="error" style="color:red; float:left"><?php echo isset($errors['customer_phone']) ? $errors['customer_phone'] : ''; ?> </p>
                                                        <br>
                                                        </p>

                                                        <p id="billing_customer_phone_field" class="form-row form-row-last validate-required">
                                                            <label class="" for="address">Address <span class="color-red">*</span>
                                                            </label>
                                                            <input type="text" class="span6" id="address" name="address" value="<?php echo isset($address) ? $address : ''; ?>">

                                                        <p class="error" style="color:red; float:left"><?php echo isset($errors['address']) ? $errors['address'] : ''; ?> </p>
                                                        <br>
                                                        </p>

                                                        <div class="clear"></div>
                                                        <div class="clear"></div>
                                                        <div class="clear"></div> <br>
                                                    </div>
                                            <?php }
                                            } ?>

                                            <?php
                                            if (!isset($_SESSION['id'])) {
                                            ?>
                                                <div class="woocommerce-billing-fields">
                                                    <h3>Billing Details</h3>

                                                    <p id="billing_customer_name_field" class="form-row form-row-first validate-required">
                                                        <label class="" for="customer_name">Name <span class="color-red">*</span>
                                                        </label>
                                                        <input type="text" class="span6" id="customer_name" name="customer_name" value="<?php echo isset($name) ? $name : ''; ?>">
                                                    <p class="error" style="color:red; float:left"><?php echo isset($errors['customer_name']) ? $errors['customer_name'] : ''; ?> </p>
                                                    <br>
                                                    </p>

                                                    <p id="billing_customer_email_field" class="form-row form-row-first validate-required">
                                                        <label class="" for="customer_email">Email <span class="color-red">*</span>
                                                        </label>
                                                        <input type="text" class="span6" id="customer_email" name="customer_email" value="<?php echo isset($email) ? $email : ''; ?>">
                                                    <p class="error" style="color:red; float:left"><?php echo isset($errors['customer_email']) ? $errors['customer_email'] : ''; ?> </p>
                                                    <br>
                                                    </p>

                                                    <p id="billing_customer_phone_field" class="form-row form-row-last validate-required">
                                                        <label class="" for="customer_phone">Phone <span class="color-red">*</span>
                                                        </label>
                                                        <input type="text" class="span6" id="customer_phone" name="customer_phone" value="<?php echo isset($phone) ? $phone : ''; ?>">

                                                    <p class="error" style="color:red; float:left"><?php echo isset($errors['customer_phone']) ? $errors['customer_phone'] : ''; ?> </p>
                                                    <br>
                                                    </p>

                                                    <p id="billing_customer_phone_field" class="form-row form-row-last validate-required">
                                                        <label class="" for="address">Address <span class="color-red">*</span>
                                                        </label>
                                                        <input type="text" class="span6" id="address" name="address" value="<?php echo isset($address) ? $address : ''; ?>">

                                                    <p class="error" style="color:red; float:left"><?php echo isset($errors['address']) ? $errors['address'] : ''; ?> </p>
                                                    <br>
                                                    </p>

                                                    <div class="clear"></div>
                                                    <div class="clear"></div>
                                                    <div class="clear"></div> <br>
                                                </div>
                                            <?php
                                            }

                                            ?>

                                        </div>
                                    </div>

                                    <!-----------------Display product--------------->
                                    <h3 id="order_review_heading">Your order</h3>
                                    <div id="order_review" style="position: relative;">
                                        <table cellspacing="0" class="shop_table cart">
                                            <thead>
                                                <tr>
                                                    <th class="product-remove">&nbsp;</th>
                                                    <th class="product-thumbnail">&nbsp;</th>
                                                    <th class="product-name">Product</th>
                                                    <th class="product-price">Price</th>
                                                    <th class="product-quantity">Quantity</th>
                                                    <th class="product-subtotal">Total</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php

                                                foreach ($_SESSION['save_to_cart'] as $key => $item) {
                                                ?>
                                                    <tr class="cart_item">
                                                        <td class="product-remove">
                                                            <a title="Remove this item" class="remove" href="../cart/delete_cart.php?id=<?php echo $item['id']; ?>">×</a>
                                                        </td>

                                                        <td class="product-thumbnail">
                                                            <?php echo '<img src="../../backend/theme/upload/product/' . $item['image'] . ' ">'; ?>
                                                        </td>

                                                        <td class="product-name">
                                                            <a href="single-product.html"><?php echo $item['name']; ?></a>
                                                        </td>

                                                        <td class="product-price">
                                                            <span class="amount">$<?php echo $item['price']; ?></span>
                                                        </td>

                                                        <td class="product-quantity">
                                                            <div class="quantity buttons_added">
                                                                <a class="minus" href="../cart/update_quantity.php?id=<?php echo $item['id']; ?>&type=decre"> - </a>

                                                                <input type="number" size="4" class="input-text qty text" title="Qty" value="<?php echo $item['quantity']; ?>" name="quantity" id="quantity" min="1" step="1">
                                                                <a class="minus" href="../cart/update_quantity.php?id=<?php echo $item['id']; ?>&type=incre"> + </a>
                                                            </div>
                                                        </td>

                                                        <td class="product-subtotal">

                                                            <span class="amount">
                                                                <?php echo '$' . $total
                                                                ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <tr>
                                                    <td class="actions" colspan="6">

                                                        <!-- <input type="submit" value="Update Cart" name="update_cart" class="button"> -->
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-----------------End display product--------------->

                                    <div id="order_review" style="position: relative;">
                                        <table class="shop_table">
                                            <thead>
                                                <tr>
                                                    <th class="product-name">Product</th>
                                                    <th class="product-total">Total</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="shipping">
                                                    <th>Shipping and Handling</th>
                                                    <td>Free Shipping
                                                        <input type="hidden" class="shipping_method" value="free_shipping" id="shipping_method_0" data-index="0" name="shipping_method[0]">
                                                    </td>
                                                </tr>
                                                <tr class="order-total">
                                                    <th>Order Total</th>
                                                    <td><strong>
                                                            <span class="amount">
                                                                <?php
                                                                if (isset($_SESSION['save_to_cart']) && $_SESSION['save_to_cart'] > 0) {
                                                                    echo $countProduct;
                                                                } else {
                                                                    echo 0;
                                                                }
                                                                ?>

                                                            </span>
                                                        </strong>
                                                    </td>
                                                </tr>
                                                <tr class="order-total">
                                                    <th>Money Total</th>
                                                    <td><strong><span class="amount"><?php echo '$' . $countMoney; ?></span></strong> </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <div id="payment">
                                            <ul class="payment_methods methods">

                                                <li class="payment_method_cheque">
                                                    <input type="radio" id="status" name="status" value="0">
                                                    <label for="payment_method_cheque">Payment on delivery <span class="color-red">*</span> </label><br>
                                                    <p class="error" style="color:red; float:left"><?php echo isset($errors['status']) ? $errors['status'] : ''; ?> </p>
                                                    <br>
                                                </li>
                                            </ul>

                                            <div class="form-row place-order">
                                                <input type="submit" data-value="Place order" value="Place order" id="place_order" name="woocommerce_checkout_place_order" class="button alt"
                                                onclick="return confirm('Are you sure to buy ?');">
                                            </div>
                                            <div class="clear"></div>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php  } else { ?>
                            <h1 class="cart-empty">Please select a product to payments</h1>
                        <?php  } ?>
                    </div>
                </div>
                <!--------end main---------->
            </div>
        </div>
    </div>
    <?php
    ob_end_flush();
    include "../common/footer_top.php";
    include "../common/footer_bottom.php";
    include "../common/footer_js.php";
    ?>
</body>

</html>