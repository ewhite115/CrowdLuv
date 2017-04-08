

<?php
    $mobileStatus = $CL_model->getMobileValidityForFollower($clRequestInformation->getLoggedInUserObj()['crowdluv_uid'] );
    $emailStatus = $CL_model->getEmailValidityForFollower($clRequestInformation->getLoggedInUserObj()['crowdluv_uid'] );
?>



<BR>



    <div class="row">


        <div class="col-xs-12 col-sm-10 col-sm-offset-1 clwhitebg crowdluvsection">
           

            <!-- Account connections -->

            <div class="row">
                <div class="col-xs-12 crowdluvsection">
                    
                    <!-- Facebook Account Connection Status -->
   
                    <?php if( ! $clRequestInformation->clFacebookHelper->checkFacebookPermissions($clRequestInformation->clFacebookHelper->getFacebookSession(), CrowdLuvFacebookHelper::$talentFacebookPermissionScope )){ ?>
                        <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                            Facebook:
                        </h1> 
                        <span>Not Connected <a href="<?php echo $clRequestInformation->clFacebookHelper->getLoginUrl(); ?>"> Authorize Facebook Permissions </a><span>
                    <?php } ?>
                    
                    <!-- Spotify Account Connection Status -->
                    <?php if(! $clRequestInformation->clSpotifyHelper->getSpotifySession()){ ?>
                        <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                            [  Spotify  ]
                        </h1>                                        
                        <span><a href="<?php echo $clRequestInformation->clSpotifyHelper->getLoginUrl(); ?>"> Connect Your Spotify Account</a><span>
                    <?php } ?> 

                    <!-- YouTube Account Connection Statue -->
                         
                    <?php if(! $clRequestInformation->clYouTubeHelper->getYouTubeSession()){ ?>
                        <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                            [  YouTube  ] 
                        </h1>               
                        <span><a href="<?php echo $clRequestInformation->clYouTubeHelper->getAuthUrl(); ?>"> Connect Your YouTube Account</a><span>
                    <?php } ?>


                </div>


            </div>


       <!-- Block to show status of contact info -->

            <div class="row">
                <div class="col-xs-12 crowdluvsection">
                    <h1 class="cl-major-heading cl-textcolor-standout inline-block">[ Mobile ]</h1>
                    <span> 
                    <?php if($mobileStatus=="invalid") { ?>Invalid<?php } ?>
                    <?php if($mobileStatus=="valid") { ?>Valid<?php } ?>
                    <?php if($mobileStatus=="verified") { ?>Verified<?php } ?>
                    </span>
                 

                    <h1 class= "cl-major-heading cl-textcolor-standout inline-block">[ Email ] </h1>
                    <span>
                    <?php if($emailStatus=="invalid") { ?>Invalid<?php } ?>
                    <?php if($emailStatus=="valid") { ?>Valid<?php } ?>
                    <?php if($emailStatus=="verified") { ?>Verified<?php } ?>

                    </span>

                    <a href="mypreferences">My Settings --> </a>

                </div>

                
            </div>

            <?php if($mobileStatus == "invalid" || $emailStatus == "invalid"){ ?>
            <div class="row">
                <div class="col-xs-12">
                    <p class="cl-textcolor-standout">
                        Providing valid contact info allows you to sign up to receive alerts from your favorite acts
                    </p>
                </div>

            </div>
            <?php } ?>
        </div>
    </div>










