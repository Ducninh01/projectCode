<?php
    $currentUrl = getCurrentURl();
    if ($currentUrl == 'login.php') {
?>
    <div class="login-extra">
        <a href="../auth/forgot_password.php">Reset Password</a>
    </div>
<?php }

    if ($currentUrl == 'signup.php') {
?>
    <div class="login-extra">
        Already have an account? <a href="../auth/login.php">Login to your account</a>
    </div>
<?php }
    if ($currentUrl == 'forgot_password.php') { ?>
    <div class="login-extra">
        Already have an account? <a href="../auth/login.php">Login to your account</a>
    </div>
<?php }
?>