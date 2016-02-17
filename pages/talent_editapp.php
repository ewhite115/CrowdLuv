<?php 
    //require_once "../inc/cl_bootstrap.php"; 

    $pageTitle = "CrowdLuv";
    $clResponseInformation->clCiteSection = "talent";
 
    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');
        // if(! $CL_LOGGEDIN_USER_UID) { echo "no logged in user(?)"; exit; } 


    $badpatherr=false; //flag to indicate the user tried to upload an image in unsupported file format
    $vurl_err=false;  //flag to indicate whether the user requested an invalid or unavailable vanity url
    //Get the landing page settings for this talent
    $tlpgsettings = $CL_model->get_talent_landingpage_settings($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);
    
    
    //If this is a postback and the user has specified a new message, insert a new row into the CL db for the new msg   
    if(isset($_POST['newmsg']) &&  $_POST['newmsg'] != ""  && ($_POST['newmsg'] != $tlpgsettings['message'])) { $CL_model->update_talent_landingpage_message($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'], $_POST['newmsg']);  }    

    //If this is a postback and the user is setting a new image for their landing page ...
    do if(isset($_FILES['newimg']) && basename($_FILES['newimg']['name']) != "" & basename($_FILES['newimg']['name']) != $tlpgsettings['image']) {
        //Check that it is a .jpg or .png
        $extension = pathinfo( $_FILES["newimg"]["name"], PATHINFO_EXTENSION);
        if(! ($extension == "jpg" || $extension == "png")) { $badpatherr=true; break;  }

        //In case the file storage direcories have not been created for this talent (which should only be the case for the first few talent in the db), call the function that will create them if they dont exist
        $CL_model->create_new_cl_talent_files($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);
        //First, attempt to move the image into their storage folder on the server
        $uploaddir = ROOT_PATH . 'crowdluvdata/talent/' . $CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'] . '/landingpage_images/';
        $uploadfile = $uploaddir . basename($_FILES['newimg']['name']);
        //echo "uploadfile=" . $uploadfile;
        if (move_uploaded_file($_FILES['newimg']['tmp_name'], $uploadfile)) {
            //echo "File is valid, and was successfully uploaded.\n";
        } else {
            echo "Possible file upload attack!\n";
        }
        //Now update the CL database to reflect the new img file name
        $CL_model->update_talent_landingpage_image($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'], basename($_FILES['newimg']['name']));        


    } while(false);   //wrap the if block in a do-while so that we can break out on bad file extensions

    //Get the landing page settings for this talent  (again)
    $tlpgsettings = $CL_model->get_talent_landingpage_settings($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);
    //var_dump($tlpgsettings); exit;
    if($tlpgsettings['image'] == "facebookprofile") $tlpimg = "https://graph.facebook.com/" . $CL_ACTIVE_MANAGED_TALENT['fb_pid'] . "/picture?access_token=" . $facebookSession->getToken();
    else if ($tlpgsettings['image'] != "" && $tlpgsettings['image'] != "default") $tlpimg = BASE_URL . 'crowdluvdata/talent/' . $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"] . '/landingpage_images/' . $tlpgsettings["image"];
    else $tlpimg = CLADDR . 'res/crowdluv_fbtab_defaulthero_820.jpg';//else $tlpimg = BASE_URL . 'crowdluvdata/default_talent_landingpage_image.jpg';
    


    include(ROOT_PATH . 'views/partial_cl_html_leader.php'); 

?>
    

<div class="row">
    <div class="col-xs-12 crowdluvsection">
    <h1>Customizing Your CrowdLuv Landing Page</h1>
    </div>
</div>

<!--  STEP 1:  Vanity URL  -->
<div class="row crowdluvsection clwhitebg">
    <div class="col-xs-12 col-sm-5">
        <h1>Step 1:  Choose a CrowdLuv URL</h1>
    </div>
    <div class="col-xs-12 col-sm-7">
        <p>
            <?php echo CLADDR;?>
                <input type="text" name="txt_crowdluv_vurl" id="txt_crowdluv_vurl" 
                                value='<?php if($CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"] == ""){ echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"];}
                                              else {echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"];} ?>'>
                </input>
                <button id="btnCheckAndSetVURL" class="cl-button-standout">Check and Set</button>
                <p class="cl-textcolor-standout" id="p-vanity-url-result"></p> 
        </p>
    </div>

</div>


<div id="div-vurl-details" class="row">
    <div class="col-xs-10 col-xs-offset-1 text-center crowdluvsection">

        <p>This will be the address of your CrowdLuv landing page, where your fans can Luv you, allowing you 
            to connect with them more effectively. You will be able to share this link on social media, email etc
        </p>

    </div>
</div>




<!--  STEP 2: Picture  -->
<div class="row crowdluvsection clwhitebg">
    <div class="col-xs-12 col-sm-4">
        <h1>Step 2:  Choose a splash picture</h1>
    </div>
    <div class="col-xs-12 col-sm-8">
        <p>Explanation </p>
    </div>
</div>


<div class="row ">
        <div class="col-sm-5 crowdluvsection">
            
            <img src="<?php echo $tlpimg;?>" style="max-width: 100%;max-height: 300px;"/>    
        </div>

        <div class="col-sm-3 crowdluvsection">  
            <p2>Upload new image</p2><br>
            <?php if($badpatherr) {             ?>
                <p style="color:red;">You uplodaded a <?php echo $extension;?> file. Only .jpg and .png files are allowed</p>
            <?php } ?>
            <form enctype="multipart/form-data" action="#" method="POST">
                <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
                <input type="file" name="newimg" id="newimg" accept="image/*"></input>
                <button type="submit" class="cl-button-standout">Add</button>
            </form>

        </div>

</div>


<!--  STEP 3: Message -->
<div class="row crowdluvsection clwhitebg">
    <div class="col-xs-12 col-sm-4">
        <h1>Step 3:  Add a Personal Message</h1>
    </div>
    <div class="col-xs-12 col-sm-8">
        <p> Current message: <?php echo $tlpgsettings['message'];?>   </p>
    </div>
</div>


<div class="row ">
    <div class="col-xs-12 crowdluvsection">
        <form enctype="multipart/form-data" action="#" method="POST">
            <textarea name="newmsg" id="newmsg" cols="34"></textarea>
            <button type="submit" class="cl-button-standout">Update</button>
        </form>

    </div>
</div>




<script>


$(document).ready(function(){

    $("#p-vanity-url-result").hide();
    $("#div-vurl-details").hide();

    //Change handler for the vanity URL textbox 
    $("#btnCheckAndSetVURL").click(function(){

        update_talent_landingpage_vurl($("#txt_crowdluv_vurl").val(), function(response){            
            //On callback: 
            console.log('in callback for vurl. received response:');  console.log(response);
            //set the textbox to the returned/sanitized vurl
            $("#txt_crowdluv_vurl").val(response.vurl);
            //fade in and out the result message
            $("#p-vanity-url-result").html(response.description).fadeIn(1800);
            

        });

    });

    //on focus handler for the vanity URL textbox  to show detail div
    $("#txt_crowdluv_vurl").focus(function(){

        $("#div-vurl-details").show();
    });
    $("#txt_crowdluv_vurl").focusout(function(){

    $("#div-vurl-details").hide();
});


}); //docready


</script>



<?php include(ROOT_PATH . 'views/partial_footer.php') ?>
