


            <form method="post" action="<?php echo BASE_URL; ?>updateuserinfo.php">
            <div class="clwhitebg crowdluvsection">
                <h2>Contact Settings</h2>
                <div class="row">
                    <div class="col-ms-5">
                        <p>Email Address</p>
                        <input class="cl_graybackground" type="text" name="email" id="email" value="<?php echo $CL_LOGGEDIN_USER_OBJ['email'];?>"></input>
                    </div>
                    <div class="col-ms-7">
                        <p>Allow CrowdLuv Contact?</p>
                        <p2><input type="radio" name="allow_cl_email" value="all" <?php if($CL_LOGGEDIN_USER_OBJ['allow_cl_email']=='all'){ ?> checked="true" <?php } ?>>All Updates</input></p2>
                        <p2><input type="radio" name="allow_cl_email" value="onlycritical" <?php if($CL_LOGGEDIN_USER_OBJ['allow_cl_email']=='onlycritical'){ ?> checked="true" <?php } ?>> Only Critical Updates</input></p2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-ms-5">
                        <p>Mobile Number</p>
                        <input class="cl_graybackground" type="text" name="mobile" id="mobile" value="<?php echo $CL_LOGGEDIN_USER_OBJ['mobile'];?>"></input>
                        <p2 class="cl-textcolor-standout">Receive SMS alerts when your favorite performers come to your town</p2>
                    </div>
                    <div class="col-ms-7">
                            <p></p>
                        <p2><input type="radio" name="allow_cl_sms" value="all"  <?php if($CL_LOGGEDIN_USER_OBJ['allow_cl_sms']=='all'){ ?>  checked="true" <?php } ?>>All Updates</input></p2>
                        <p2><input type="radio" name="allow_cl_sms" value="onlycritical" <?php if($CL_LOGGEDIN_USER_OBJ['allow_cl_sms']=='onlycritical'){ ?>  checked="true" <?php } ?>>Only Critical Updates</input></p2>
                        
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <br><label for="firstname">Name</label><input size="10" class="cl_graybackground" type="text" name="firstname" id="firstname" value="<?php echo $CL_LOGGEDIN_USER_OBJ['firstname'];?>"><input size="10" class="cl_graybackground" type="text" name="lastname" id="lastname" value="<?php echo $CL_LOGGEDIN_USER_OBJ['lastname'];?>">
                        <br><label for="city">Your City</label><input class="cl_graybackground" type="text" name="city" id="city" value="<?php echo $CL_LOGGEDIN_USER_OBJ['location_fbname'];?>" disabled></td>
                        <br><label for="birthdate">Birth date</label><input class="cl_graybackground" type="text" name="birthdate" id="birthdate" disabled value="<?php echo $CL_LOGGEDIN_USER_OBJ['birthdate'];  ?>">
                        <br><label for="rltsp">Relationship</label><input class="cl_graybackground" type="text" name="rltsp" id="rltsp" disabled value="<?php echo $CL_LOGGEDIN_USER_OBJ['fb_relationship_status'];  ?>">
               

                    </div>
                </div>

                <div class="row">
                    <div class="col-ms-12">
                        <br><p class="cl-textcolor-standout"
                            Providing your contact info will allow you to request to receive alerts from your favorite acts when they come to your town. Your phone and email will not be shared with anyone on CrowdLuv
                        </p><br><br>
                        <!-- <input type="submit" value="Submit Changes">  -->
                        <p2 visible="false" id="p_settingsupdated">Your settings have been updated</p2>
                    </div>
                </div>
            </form>
            </div>



<script type="text/javascript">


   function animate_settingsupdated_notification(){
       $("#p_settingsupdated").fadeIn(1800, function(){
            $(this).fadeOut(1800);
        });
   }

    $(document).ready(function(){  

        $("#p_settingsupdated").hide();
        //Change handler for the "Allow email contact from CrowdLuv" radio options
        $("[name=allow_cl_email]").change(function(){
            update_follower_setting(<?php echo $CL_LOGGEDIN_USER_OBJ['crowdluv_uid']; ?>, "allow_cl_email", $(this).val());
            animate_settingsupdated_notification();
        });

        //Change handler for the "Allow SMS contact from CrowdLuv" radio options
        $("[name=allow_cl_sms]").change(function(){
            update_follower_setting(<?php echo $CL_LOGGEDIN_USER_OBJ['crowdluv_uid']; ?>, "allow_cl_sms", $(this).val());
            animate_settingsupdated_notification();
        });

        //Change handler for the email address text box 
        $("[name=email]").change(function(){
            update_follower_setting(<?php echo $CL_LOGGEDIN_USER_OBJ['crowdluv_uid']; ?>, "email", $(this).val());
            animate_settingsupdated_notification();
        });

        //Change handler for the mobile number box
        $("[name=mobile]").change(function(){
            //TODO:  validate ph# and give feedback
            update_follower_setting(<?php echo $CL_LOGGEDIN_USER_OBJ['crowdluv_uid']; ?>, "mobile", $(this).val());
            animate_settingsupdated_notification();
        });

        //Change handler for the "Allow SMS contact from CrowdLuv" radio options
        $("[name=firstname]").change(function(){
            update_follower_setting(<?php echo $CL_LOGGEDIN_USER_OBJ['crowdluv_uid']; ?>, "firstname", $(this).val());
            animate_settingsupdated_notification();
        });

        //Change handler for the "Allow SMS contact from CrowdLuv" radio options
        $("[name=lastname]").change(function(){
            update_follower_setting(<?php echo $CL_LOGGEDIN_USER_OBJ['crowdluv_uid']; ?>, "lastname", $(this).val());
            animate_settingsupdated_notification();
        });

    });

</script>