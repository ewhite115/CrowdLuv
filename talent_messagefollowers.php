<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/config.php"); 
    include(ROOT_PATH . 'inc/header.php');


    if(! $fb_user) { echo "user is null"; exit;  } 
    if(! $fb_user_pages) { echo "fb_user_pages is null";   }
    if(! isset($_GET['crowdluv_tid'])) {echo "no crowdluv_tid passed in"; exit;  }
    if(! isset($_GET['city'])) {echo "no city passed in"; exit;  }
    $city=$_GET['city'];

    $citystats= get_city_stats_for_talent($CL_CUR_TGT_TALENT['crowdluv_tid'], $city, 5)

?>

 
    <div class="crowdluvsection">
        <div style="display:inline-block">
            <h1>Message Followers</h1>
            <p><?php echo $city;?> - <?php echo $citystats['followercount'];?> Total Followers<br><br>
           
        </div>

        <div style="display:inline-block;text-align:right;">
            <h1><?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?></h1>
            <img src='https://graph.facebook.com/<?php echo $CL_CUR_TGT_TALENT['fb_pid']; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'>
        </div>
       </br>
    </div>

    <div style="display:inline-block;width:100%;">

        <div class="crowdluvsection" style="text-align:left;width:30%;margin:5px;display:inline-block;">
            <h2>Step 1: Build your audience</h2>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['followercount'];?></td><td style="width:70%;"> in <?php echo $city;?></td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;">nnn</td><td style="width:70%;"> 
            within         <select>
                           <option value="5">5</option>
                           <option value="10">10</option>
                           <option value="20">20</option>
                           
                    </select>  mile radius </td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;">nnn</td><td style="width:70%;"> will travel from afar</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['female'];?></td><td style="width:70%;"> Female</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['male'];?></td><td style="width:70%;"> Male</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['relationship'];?></td><td style="width:70%;"> Relationship</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['single'];?></td><td style="width:70%;"> Single</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['12to17'];?></td><td style="width:70%;"> Age Group 12-17</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['18up'];?></td><td style="width:70%;"> Age Group 18+</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['21up'];?></td><td style="width:70%;"> Age Group 21+</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['24to49'];?></td><td style="width:70%;"> Age Group 24-49</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;">nnn</td><td style="width:70%;"> New since last login</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['signedup30'];?></td><td style="width:70%;"> signed up w/in 30 days</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['signedup90'];?></td><td style="width:70%;"> signed up w/in 90 days</td><td><input type='checkbox' /></td></tr></table><br>
            <table class='cldefaulttable' ><tr><td style="width:20%;"><?php echo $citystats['signedup365'];?></td><td style="width:70%;"> signed up w/in 1 year</td><td><input type='checkbox' /></td></tr></table><br>
            


        </div>           
        
        <div style="display:inline-block;position:relative;width:65%">
        <div class="crowdluvsection" style="display:inline-block;position:relative;">
            <br>
            <h3>Your selected groups</h3>
             <p2>nn who will trvel from afar, nn of type A, nn of type B</p2><br><br>
            <h3> The message will deliver to</h3>
             <p2>nnn followers of your selected group who provided authorization to receive xy messages</p><br><br>
            <h3>Your current balance</h3>
             <p>nnn credits (get more)</p2><br><br>
            <h3>Cost to send:</h3>
             <p2>nnn credits</p2>


        </div>
        <br><br>
         <div class="crowdluvsection" style="display:inline-block;">
            <h2>Step 2: Send your message</h2>
            <p><input type="checkbox" /> Text Message &nbsp;&nbsp; &nbsp;  <input type="checkbox"> Email Message</p>
            <textarea rows='10' cols='45' style="display:inline-block;" > </textarea>

            <br> <button type='button'>Send Message</button>
        </div>

    </div>
    </div>


        


<?php include(ROOT_PATH . 'inc/footer.php') ?>