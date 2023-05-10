<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container"> <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span> </a><a class="brand" href="../dashboard/dashboard.php">Admin</a>
            <div class="nav-collapse">
                <ul class="nav pull-right">
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i> <?php echo isset($_SESSION["username"]) ? $_SESSION["username"] : header("location: ../index.php"); ?> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="../auth/profile.php?id=<?php echo isset($_SESSION["id"]) ? $_SESSION["id"] : ''; ?> ">Profile</a></li>
                            <li><a href="../auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>