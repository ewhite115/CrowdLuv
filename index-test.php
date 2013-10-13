<?php 

require_once("inc/config.php");
require_once("facebook-sdk/facebook.php");

include(ROOT_PATH . "inc/products.php");
$recent = get_products_recent();

$pageTitle = "CrowdLuv";
$section = "home";
include(ROOT_PATH . 'inc/header.php'); ?>



        <div class="section banner">

            <div class="wrapper">

                <!-- <img class="hero" src="<?php echo BASE_URL; ?>img/mike-the-frog.png" alt="Mike the Frog says:"> 
                <div class="button">
                    <a href="<?php echo BASE_URL; ?>shirts.php">
                        <h2>Hey, I&rsquo;m Mike!</h2>
                        <p>Check Out My Shirts</p>
                    </a>
                </div>
            -->
            </div>

        </div>




        <div class="section shirts latest">
            <div class="wrapper">

 
                <div class="crowdluv_landingpage_memberlogin_box">
                    <?php 

                    if(! $user) {

                        echo "<h1>Follower Login</h1>Already Signed up with CrowdLuv? Use your facebook account to sign in<br><br>";
                        echo "<fb:login-button show-faces=\"true\" width=\"300\" max-rows=\"1\"></fb:login-button>";

                    }
                    else {

                     echo "Welcome back to CrowdLuv, " . $user_profile['name'] . "!<br>"; 
                     echo "<br> <img src=\"https://graph.facebook.com/". $user . "/picture\"><br><br>";
                     echo "You have Luv'ed 0 of your favorite talent";
                    var_dump($user_profile);

                    }


                    ?>
                </div>
<!--
                <h2>Mike&rsquo;s Latest Shirts</h2>
                <ul class="products">
                    <?php
                        foreach(array_reverse($recent) as $product) {
                            include(ROOT_PATH . "inc/partial-product-list-view.html.php");
                        }
                    ?>
                </ul>
-->
            </div>
        </div>







<?php include(ROOT_PATH . 'inc/footer.php') ?>