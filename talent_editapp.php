<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/config.php");
    include(ROOT_PATH . 'inc/header.php');

    if(! $fb_user) { echo "fb_user is null"; exit; } 

?>
    

    <div class="fluid-row">
        <div class="col-xs-12 crowdluvsection">
        <h1>Edit App</h1>
        </div>
    </div>


    <div class="fluid-row">
        <div class="col-sm-4 crowdluvsection">
            <h2><?php echo $CL_ACTIVE_MANAGED_TALENT['fb_page_name'];?></h2>
            <p2>Edit your personal message</p2><br>
            <textarea cols="34"></textarea>
            <br>
            <p2>Current message: ... jkshdfkjhfj jahf flkdsj lkjflk sdlfj dslk flkdsjf ldjflkd fljds flj dslkfj dslkjfl dsf lsdkjf lkdsjf lkdsjf lkdsj flkdsj flkdsj flkjsdfl kjsdflk;dsj lfkkjd slkfjdsl ;fka
            </p2>

        </div>
    

        <div class="col-sm-5 crowdluvsection">
            <p2>Change your photo</p2>
            <br>
            insert image here
        </div>

        <div class="col-sm-3 crowdluvsection">  
            <p2>Upload new image</p2><br>
            <button disabled type="button">Browse</button><br>
            <p2>Link to new Image</p2><br>
            <input type="text"></input>
            <button disabled type="button">Submit</button><br>
            <p2>Make sure your photo is ..... This photo will appear on your Facebook CrowdLuv page and your CrodLuv landing page </p2>

        </div>

    </div>
    
    <div class="fluid-row">
        <div class="col-sm-6 crowdluvsection">
            <button disabled type="button">Facebook Preview</button>
            <button disabled type="button">CrowdLuv Preview</button> 
        </div>
        <div class="col-sm-6 crowdluvsection text-right">
            <button disabled type="button">Submit Changes</button>
        </div>
   </div>



<?php include(ROOT_PATH . 'inc/footer.php') ?>