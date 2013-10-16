<?php 

    require_once("inc/config.php");
    require_once("facebook-sdk/facebook.php");
    require_once("inc/cl_facebookinit.php");

    $pageTitle = "CrowdLuv";
    $section = "home";
    include(ROOT_PATH . 'inc/header.php'); 


    //Get the talent name, based on the talent ID that was passed in via query string
    parse_str($_SERVER['QUERY_STRING']);
    try {
        $fql = "SELECT name from page where page_id = " . $talentpageid;
        $ret_obj = $facebook->api(array(
                                   'method' => 'fql.query',
                                   'query' => $fql,
                                 ));
        //var_dump($ret_obj);
        $talentname = $ret_obj[0]["name"];
    } catch(FacebookApiException $e) {
        echo  "FacebookAPIException in luv.php getting talentname: " . $e; 
        error_log($e->getType());
        error_log($e->getMessage());
    }   
    
    add_follower_to_talent(get_crowdluv_uid_by_fb_uid($fb_user), get_crowdluv_tid_by_fb_pid($talentpageid));


?>

        <div class="section shirts latest">
            <div class="wrapper">
                <div class="crowdluvsection" style="text-align:center; width:60%; margin-left:auto;margin-right:auto;">
                  <?php 
                   
                    if(! $fb_user) {
                        echo "user is null"; exit;
                        //echo "<h1>Follower Login</h1>Already Signed up with CrowdLuv? Use your facebook account to sign in<br><br>";
                        //echo "<fb:login-button show-faces=\"false\" width=\"300\" max-rows=\"1\"></fb:login-button>";
                    }
                    else {
                        //echo "<h1>Hello, " . $fb_user_profile['name'] . "! Welcome to CrowdLuv</h1>"; 
                        echo '<br><img src="https://graph.facebook.com/'. $fb_user . '/picture"> &nbsp;&nbsp;';
                        echo ' Luvs &nbsp;&nbsp;';
                        echo '<img src="https://graph.facebook.com/'. $talentpageid . '/picture?access_token=' . $facebook->getAccessToken() . '"><br>';
                        echo "<br>We'll let " . $talentname . " know you want them to come to your town. Please confirm your contact information so that we can let you know when " . $talentname . " is coming to " . $fb_user_profile['location']['name'] . "<br>";
                    }
              
                  ?>

                </div>

                <form method="post" action="<?php echo BASE_URL; ?>updateuserinfo.php">
                <table>
                    <tr>
                        <th>
                            <label for="name">Name</label>
                        </th>
                        <td>
                            <input type="text" name="name" id="name" value="<?php echo $fb_user_profile['name'];  ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="email">Email</label>
                        </th>
                        <td>
                            <input type="text" name="email" id="email" value="<?php if (isset($fb_user_profile['email'])) echo $fb_user_profile['email'];  ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="mobile">Mobile</label>
                        </th>
                        <td>
                            <input type="text" name="mobile" id="mobile" value="<?php if (isset($mobile)) echo htmlspecialchars($mobile);  ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="city">Your Town</label>
                        </th>
                        <td>
                            <input type="text" name="city" id="city" value="<?php if (isset($fb_user_profile['location']['name'])) echo htmlspecialchars($fb_user_profile['location']['name']);  ?>">
                        </td>
                    </tr>

                    <tr style="display: none;">
                        <?php // the field named address is used as a spam honeypot ?>
                        <?php // it is hidden from users, and it must be left blank ?>
                        <th>
                            <label for="address">Address</label>
                        </th>
                        <td>
                            <input type="text" name="address" id="address">
                            <p>Humans (and frogs): please leave this field blank.</p>
                        </td>
                    </tr>                   
                </table>
                <input type="submit" value="Confirm">
            </form>

            </div>
        </div>


<script>
          
    //Any fb-related stuff to do once fb data has been retrieved
    $(document).bind('fbUserDataLoaded', function () {

        console.log("entering luv.php fb load handler");
        console.log("fb_loginstatus_response=");
        console.log(fb_loginstatus_response);
        console.log("fb_uid=" + fb_uid);
        console.log("fb_accesstoken=" + fb_accesstoken);
    });

</script>

<?php include(ROOT_PATH . 'inc/footer.php') ?>