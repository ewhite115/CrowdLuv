<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/config.php");
    include(ROOT_PATH . 'inc/header.php');

    if(! $CL_LOGGEDIN_USER_UID) { echo "no logged in user(?)"; exit; } 


    $badpatherr=false; //flag to indicate the user tried to upload an image in unsupported file format
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
    if($tlpgsettings['image'] == "" || $tlpgsettings['image'] == "default") $tlpimg = BASE_URL . 'crowdluvdata/talent_landingpage_images/default.jpg';
    else $tlpimg = BASE_URL . 'crowdluvdata/talent/' . $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"] . '/landingpage_images/' . $tlpgsettings["image"];




?>
    

<form enctype="multipart/form-data" action="#" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
    <div class="fluid-row">
        <div class="col-xs-12 crowdluvsection">
        <h1>Edit App</h1>
        </div>
    </div>


    <div class="fluid-row">
        <div class="col-sm-4 crowdluvsection">
            <h2><?php echo $CL_ACTIVE_MANAGED_TALENT['fb_page_name'];?></h2>
            <p2>Edit your personal message</p2><br>
            <textarea name="newmsg" id="newmsg" cols="34"></textarea>
            <br>
            <p2>Current message: <?php echo $tlpgsettings['message'];?>
            </p2>

        </div>
    

        <div class="col-sm-5 crowdluvsection">
            <p2>Change your photo</p2>
            <br>
            <img src="<?php echo $tlpimg;?>" style="max-width: 100%;"/>    
        </div>

        <div class="col-sm-3 crowdluvsection">  
            <p2>Upload new image</p2><br>
            <?php if($badpatherr) {             ?>
                <p style="color:red;">You uplodaded a <?php echo $extension;?> file. Only .jpg and .png files are allowed</p>
            <?php } ?>
            <input type="file" name="newimg" id="newimg" accept="image/*"></input><br>
            <p2>Link to new Image</p2><br>
            <input disabled type="text"></input>
            <button disabled type="button">Load</button><br>
            <p2>Make sure your photo is ..... This photo will appear on your Facebook CrowdLuv page and your CrodLuv landing page </p2>

        </div>

    </div>
    
    <div class="fluid-row">
        <div class="col-sm-6 crowdluvsection">
            <button disabled type="button">Facebook Preview</button>
            <button disabled type="button">CrowdLuv Preview</button> 
        </div>
        <div class="col-sm-6 crowdluvsection text-right">
            <button type="submit">Submit Changes</button>
        </div>
   </div>
</form>


<?php include(ROOT_PATH . 'inc/footer.php') ?>