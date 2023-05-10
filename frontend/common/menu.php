<?php
$currentUrl = getCurrentURl();
// Set page = 1 ( active button pagination)
$page = 1;
?>

<div class="mainmenu-area">
    <div class="container">
        <div class="row">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">

                    <?php
                        if ($currentUrl == 'index.php') {
                            echo '<li class="active">';
                        } else {
                            echo '<li class="">';
                        }
                    ?>
                    <a href="../home/">Home</a>
                </li>

                    <?php
                        if ($currentUrl == 'shop.php') {
                            echo '<li class="active">';
                        } else {
                            echo '<li class="">';
                        }
                    ?>
                    <a href="../home/shop.php?page=<?php echo $page ;?>">Shop</a>

                    <?php
                    $query = queryAll('categories', ' active = ' . ACTIVE . '', 'sort_order', 'ASC');
                    $resultCategory = mysqli_query($conn, $query);
                    $numRows = mysqli_num_rows($resultCategory);

                    if ($numRows > 0) {
                        while ($row = mysqli_fetch_array($resultCategory)) {
                    ?>

                    <?php
                        $currentUrl = $_SERVER['REQUEST_URI'];
                        if (strpos($currentUrl, 'category_product.php?category_id=' . $row['id']) !== false) {
                            echo '<li class="active">';
                        } else {
                            echo '<li class="">';
                        }
                    ?>
                    <a href="../home/category_product.php?category_id=<?php echo $row['id']; ?>&page=<?php echo $page ;?>"><?php echo $row['name']; ?></a>
                    </li>
                    <?php
                        }
                    } else {
                        echo '';
                    }
                    ?>
                   
<li>
<a href="">Contact us</a>    
</li>
                </ul>
            </div>
        </div>
    </div>
</div> <!-- End mainmenu area -->