<div class="header-area">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="user-menu">
                    <ul>
                        <!-------Check login------>
                        <?php if (isset($_SESSION['name_login'])) { ?>

                            <li><a href="../authen/profile.php?id=<?php echo isset($_SESSION['id']) ? $_SESSION['id'] : '' ?>">
                                    <i class="fa fa-bookmark-o"></i>
                                    <?php echo isset($_SESSION['name_login']) ? $_SESSION['name_login'] : '' ?> </a>
                            <?php } ?>
                            </li>
                            <!-------Login------>
                            <?php
                            if (!isset($_SESSION['name_login'])) {
                                echo '<li><a href="../authen/login.php"><i class="fa fa-sign-in"></i>Login</a></li>';
                            }
                            ?>
                            <!-------Register------>
                            <?php
                            if (isset($_SESSION['name_login'])) {
                                echo '';
                            } else {
                                echo '<li><a href="../authen/register.php"><i class="fa fa-lock"></i> Register</a></li>';
                            }
                            ?>
                            <!-------Checkout------>
                            <?php
                            if (isset($_SESSION['name_login'])) {
                                echo '<li><a href="../checkout/checkout.php"><i class="fa fa-credit-card"></i>Checkout</a></li>';
                            }
                            ?>
                            <!------Orders----->
                            <?php
                            if (isset($_SESSION['name_login'])) {
                                echo '<li><a href="../checkout/order_details.php"><i class="fa fa-money"></i>Orders</a></li>';
                            } else {
                                echo '';
                            }
                            ?>
                            <!------change password------>
                            <?php
                            if (isset($_SESSION['name_login'])) {

                                echo '<li><a href="../authen/change_password.php"><i class="fa fa-gear"></i> Change password</a></li>';
                            }
                            ?>
                            <!---------logout---------->
                            <?php
                            if (isset($_SESSION['name_login'])) {
                                echo '<li><a href="../authen/logout.php"><i class="fa fa-sign-out"></i>Logout</a></li>';
                            }
                            ?>
                    </ul>
                </div>
            </div>

            <div class="col-md-4">
                <div class="header-right">
                    <ul class="list-unstyled list-inline">
                        <!-- <li class="dropdown dropdown-small">
                            <a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" href="#"><span class="key">currency :</span><span class="value">USD </span><b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">USD</a></li>
                                <li><a href="#">INR</a></li>
                                <li><a href="#">GBP</a></li>
                            </ul>
                        </li> -->

                        <!-- <li class="dropdown dropdown-small">
                            <a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" href="#"><span class="key">language :</span><span class="value">English </span><b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">English</a></li>
                                <li><a href="#">French</a></li>
                                <li><a href="#">German</a></li>
                            </ul>
                        </li> -->

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End header area -->