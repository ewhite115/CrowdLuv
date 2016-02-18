

    <form method="post" action="<?php echo BASE_URL; ?>updateuserinfo.php">
    <div class="clwhitebg crowdluvsection">
        

        <div class="row">
            <div class="col-xs-12 col-lg-5">
                <h2>Mobile Number</h2>
                <input class="cl_graybackground" type="tel" name="mobile" id="mobile" value="<?php echo $clRequestInformation->getLoggedInUserObj()['mobile'];?>"></input>
                <span id="phone_valid"></span>
            </div>

            <div class="col-xs-12 col-lg-7">
                <p>
                    <input type="radio" name="allow_cl_sms" value="all"  <?php if($clRequestInformation->getLoggedInUserObj()['allow_cl_sms']=='all'){ ?>  checked="true" <?php } ?>>All Updates</input> 
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="allow_cl_sms" value="onlycritical" <?php if($clRequestInformation->getLoggedInUserObj()['allow_cl_sms']=='onlycritical'){ ?>  checked="true" <?php } ?>>Only Critical Updates</input>
                </p>   

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-lg-5">
                <h2>Email Address</h2>
                <input class="cl_graybackground" type="email" name="email" id="email" value="<?php echo $clRequestInformation->getLoggedInUserObj()['email'];?>"></input>
            </div>
            <div class="col-xs-12 col-lg-7">
                <p>
                    <input type="radio" name="allow_cl_email" value="all" <?php if($clRequestInformation->getLoggedInUserObj()['allow_cl_email']=='all'){ ?> checked="true" <?php } ?>>All Updates</input> 
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="allow_cl_email" value="onlycritical" <?php if($clRequestInformation->getLoggedInUserObj()['allow_cl_email']=='onlycritical'){ ?> checked="true" <?php } ?>> Only Critical Updates</input>
                </p>

            </div>
        </div>


        <div class="row">
            <div class="col-xs-12">
                <p>
                    <label for="firstname">Name</label><input size="10" disabled class="cl_graybackground" type="text" name="firstname" id="firstname" value="<?php echo $clRequestInformation->getLoggedInUserObj()['firstname'];?>"><input size="10" class="cl_graybackground" type="text" disabled name="lastname" id="lastname" value="<?php echo $clRequestInformation->getLoggedInUserObj()['lastname'];?>">
                    &nbsp;&nbsp;&nbsp;
                    <label for="city">Your City</label><input class="cl_graybackground" type="text" name="city" id="city" value="<?php echo $clRequestInformation->getLoggedInUserObj()['location_fbname'];?>" disabled></td>
                </p>
                <!-- <br><label for="birthdate">Birth date</label><input class="cl_graybackground" type="text" name="birthdate" id="birthdate" disabled value="<?php echo $clRequestInformation->getLoggedInUserObj()['birthdate'];  ?>">
                <br><label for="rltsp">Relationship</label><input class="cl_graybackground" type="text" name="rltsp" id="rltsp" disabled value="<?php echo $clRequestInformation->getLoggedInUserObj()['fb_relationship_status'];  ?>">
                -->
       
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">               
                <p visible="false" id="p_settingsupdated">Your settings have been updated</p>
                <br>
            </div>
        </div>

    </form>
    </div>



<script src="<?php echo BASE_URL;?>js/PhoneFormat.js"></script> 
<script src="<?php echo BASE_URL;?>js/userinfoform.js"></script> 

