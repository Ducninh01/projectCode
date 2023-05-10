<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <?php
            $currentUrl = getCurrentURl();

            if ($currentUrl == 'login.php') {
            ?>
                <a class="brand" href="../auth/login.php">Admin</a>
            <?php
            }
            ?>

            <?php
            if ($currentUrl == 'forgot_password.php') {
            ?>
                <a class="brand" href="../auth/login.php">Admin</a>
            <?php }

            if ($currentUrl == 'reset_password.php') { ?>
                <a class="brand" href="../auth/login.php">Admin</a>
            <?php }
            if ($currentUrl == 'error.php') {
            ?>
                <a class="brand" href="../auth/login.php">Admin</a>
                <div class="nav-collapse">
                    <ul class="nav pull-right">
                        <li class="">
                            <a href="../../index.php" class="">
                                <i class="icon-chevron-left"></i>Login
                            </a>
                        </li>
                    </ul>
                </div>
            <?php  } ?>
        </div>
    </div>
</div>