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

        <div class="crowdluvsection" style="text-align:left;width:35%;margin:5px;display:inline-block;">
          <h1>Build your audience</h1>
          <p>Create one or more target groups to receive this message</p>
                
            <h2>Select target area / radius</h2>
            <table class='cldefaulttable' style="width:100%;" ><tr><td style="width:15%;"><label id="lbl_areacount"><?php echo $citystats['followercount'];?></label></td><td style="width:70%;"> in <?php echo $city;?></td><td><input type='checkbox' checked disabled="true" class="areaoptions" id="cb_area"/></td></tr></table><br>
            <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_extareacount"></label></td><td style="width:70%;">plus all within<select class="areaoptions" id="select_distance"><option value="5">5</option><option value="10">10</option><option value="20">20</option></select>  mile radius </td><td><input type='checkbox' class="areaoptions" id="cb_distance" /></td></tr></table><br>
            <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_extareacountafar"></label></td><td style="width:70%;"> ..who are willing to travel that far</td><td><input type='checkbox' disabled="true" class="areaoptions" id="cb_fromafar" /></td></tr></table><br>

            <h2>Select gender(s) to target in this group</h2>
            <table class='cldefaulttable' style="width:100%;" ><tr><td style="width:15%;"><label id="lbl_femalecount"><?php echo $citystats['female'];?></label></td><td style="width:70%;"> Female</td><td><input type='checkbox' checked class="demooptions" id="cb_female" /></td></tr></table><br>
            <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_malecount"><?php echo $citystats['male'];?></label></td><td style="width:70%;"> Male</td><td><input type='checkbox' checked class="demooptions" id="cb_male" /></td></tr></table><br>
            
            <h2>Select relationship statuses to target in this group</h2>
            <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_relationshipcount"><?php echo $citystats['relationship'];?></label></td><td style="width:70%;"> Relationship, Engaged, Married</td><td><input type='checkbox' checked class="demooptions" id="cb_relationship"/></td></tr></table><br>
            <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_singlecount"><?php echo $citystats['single'];?></label></td><td style="width:70%;"> Single, Divorced</td><td><input type='checkbox' checked class="demooptions" id="cb_single"/></td></tr></table><br>
            
            <h2>Select age range to target in this group (un-check for all ages)</h2> 
            <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_agerangecount"></label></td><td style="width:70%;"> Age Group <input type="text" class="demooptions" id="txt_minage" size="2" value="13" /> - <input type="text" class="demooptions" id="txt_maxage" size="2" value="100" /> </td><td><input type='checkbox' class="demooptions" id="cb_agerange" /></td></tr></table><br>            
          

            <h2>Target this group based on signup date</h2>          
            <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_signupanydatecount"></label></td><td style="width:70%;"> Signed up any time</td><td><input type='radio' name="signup" checked class="demooptions" id="cb_signupanydate"/></td></tr></table><br>
            <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_signedup30count"><?php echo $citystats['signedup30'];?></label></td><td style="width:70%;"> signed up w/in 30 days</td><td><input type='radio' name="signup" class="demooptions" id="cb_30days"/></td></tr></table><br>
            <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_signedup90count"><?php echo $citystats['signedup90'];?></label></td><td style="width:70%;"> signed up w/in 90 days</td><td><input type='radio' name="signup" class="demooptions" id="cb_90days" /></td></tr></table><br>
            <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_signedup365count"><?php echo $citystats['signedup365'];?></label></td><td style="width:70%;"> signed up w/in 1 year</td><td><input type='radio' name="signup" class="demooptions" id="cb_365days"/></td></tr></table><br>

         

        </div>           
        


        <div style="display:inline-block;position:relative;width:55%">
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



<script>

    //An array of var's called targetDemoGroups  which contains targetDemoGroup objects
    //targetDemoGroup objects contain info about criteria and list of CL followers included based
    //on that criteria
    var targetsets = new Array();
    var working_targetset = new Array();

    //messageAudience object contains an array of all the CL followers who are ultimately 
    //receive the message.  The "This message will deliver to" section will summarize contents of
    //this object and this object will be sentoff to server 
    var message_audience = new Array();

    //When target area changes:
        //call ajax to get a new working set
        //do the things mentioned below for when filters change

    //When any of the target filters changes:
        //Re-calculate the working target set by 'filter'ing against the criteria 
            //  set an "included" flag to false
            //male/female filtering shoudl be based on combined values (but keep separate rows to show stats)

        //Re-calculate and display the statistics for each category

    //When talent clicks add:
        //add this working set to an array of target sets
        //also, merge the new working set with any previous target sets
        //update the "your selected groups" by calling a funtion that gets a text description of each targetset
        //update the "message will deliver to" section by calling function that gives text desc of merged set       

    function redisplayWorkingTargetSetStatistics(){

        //Determine how many of each type are still "included" in the working target set and 
        //display that ifo in the left column of the table
        //console.log("entering redisplay...()  workingtarget=");
        //console.log(working_targetset);
        var areacount=0;
        var extareacount=0;
        var extareaafarcount=0;
        var femalecount=0;
        var malecount=0;
        var relationshipcount=0;
        var singlecount=0;
        var agerangecount=0;
        var signupanydatescount=0;
        var signup30count=0;
        var signup90count=0;
        var signup365count=0;


       for (var i = 0; i < working_targetset.length; i++) {
            //if this follower is excluded from the target set, skip over them
            if( ! working_targetset[i]['included']) continue;

            areacount++;
            extareacount++;

            if( working_targetset[i]['gender'] == "female") femalecount++ ;
            if( working_targetset[i]['gender'] == "male") malecount++ ;
            
            if(   ( working_targetset[i]['fb_relationship_status'] == "In a relationship" ||
                    working_targetset[i]['fb_relationship_status'] == "Engaged" ||
                    working_targetset[i]['fb_relationship_status'] == "Married" )) relationshipcount++;

            if( (working_targetset[i]['fb_relationship_status'] == "Single" ||
                 working_targetset[i]['fb_relationship_status'] == "Divorced" )) singlecount++ ;
            
            var age = ((Date.now() - new Date(working_targetset[i]['birthdate'])) / (31557600000));
            var min_age = $("#txt_minage").val();
            var max_age = $("#txt_maxage").val(); 
            //console.log("age" + age + " maxage:"+ max_age + " minage: " + min_age);
            if((age >= min_age & age <= max_age)) agerangecount++ ;   

            //signup date
            var daysfromsignup = ((Date.now() - new Date(working_targetset[i]['signupdate'])) / (31557600000)) * 365;
            //console.log("daysfromsignup:"+ daysfromsignup);
            if((daysfromsignup > 30)) signup30count++ ;               
            if((daysfromsignup > 90)) signup90count++ ;               
            if((daysfromsignup > 365)) signup365count++;               
            signupanydatescount++;

        }//end for

        //console.log("finished counting.. female:" + femalecount + "; male:" + malecount + "; relationship:" + relationshipcount + "; single:"+singlecount);
        //Now we hav the counts,  display them...
        $("#lbl_areacount").text(areacount);
        $("#lbl_femalecount").text(femalecount);
        $("#lbl_malecount").text(malecount);
        $("#lbl_relationshipcount").text(relationshipcount);
        $("#lbl_singlecount").text(singlecount);
        $("#lbl_agerangecount").text(agerangecount);
        $("#lbl_signupanydatecount").text(signupanydatescount);
        $("#lbl_signedup30count").text(signup30count);
        $("#lbl_signedup90count").text(signup90count);
        $("#lbl_signedup365count").text(signup365count);


    }

    function refilterWorkingTargetSet(){
        //Re-calculate the working target set by 'filter'ing against the criteria 

        //Start by assuming everyone is included, then progressively exclude
        //  them if they dont meet the current criteria

        for (var i = 0; i < working_targetset.length; i++) {
            //Default includsion to true
            working_targetset[i]['included'] = true;
            //Exclude if male/female
            if( working_targetset[i]['gender'] == "female" & ! $("#cb_female").is(':checked')) working_targetset[i]['included'] = false ;
            if( working_targetset[i]['gender'] == "male" & ! $("#cb_male").is(':checked')) working_targetset[i]['included'] = false ;
            //Exclude those in reltionship
            if( ! $("#cb_relationship").is(':checked') &
                  ( working_targetset[i]['fb_relationship_status'] == "In a relationship" ||
                    working_targetset[i]['fb_relationship_status'] == "Engaged" ||
                    working_targetset[i]['fb_relationship_status'] == "Married" )) working_targetset[i]['included'] = false ;
            //Exclude single, divorced
            if(! $("#cb_single").is(':checked') & 
                (working_targetset[i]['fb_relationship_status'] == "Single" ||
                 working_targetset[i]['fb_relationship_status'] == "Divorced" )) working_targetset[i]['included'] = false ;

            //Exclude if an age range is specified and they dont meet it
            var age = ((Date.now() - new Date(working_targetset[i]['birthdate'])) / (31557600000));
            var min_age = $("#txt_minage").val();
            var max_age = $("#txt_maxage").val(); 
            //console.log("age" + age + " maxage:"+ max_age + " minage: " + min_age);
            if($("#cb_agerange").is(':checked') & (age < min_age || age > max_age)) working_targetset[i]['included'] = false ;   

            //Exclude on signup date
            var daysfromsignup = ((Date.now() - new Date(working_targetset[i]['signupdate'])) / (31557600000)) * 365;
            //console.log("daysfromsignup:"+ daysfromsignup);
            if($("#cb_30days").is(':checked') & (daysfromsignup > 30)) working_targetset[i]['included'] = false ;               
            if($("#cb_90days").is(':checked') & (daysfromsignup > 90)) working_targetset[i]['included'] = false ;               
            if($("#cb_365days").is(':checked') & (daysfromsignup > 365)) working_targetset[i]['included'] = false ;               

        }//end for

        console.log("Outputting re-filtered working_targetset:")
        console.log(working_targetset);


        //try using this jquery later for easier loop
        /*$.each(yourArray, function(index, value) {
          // do your stuff here
        });*/

    }
  

    function retrievefollowerdata() {      
        console.log("entering retrievefollowerdata");
        //ajax call to get back a JSON object of *all* the followers in the targeted area + radius
        
        var qopts = { crowdluv_tid: "<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid'];?>", city: "<?php echo $city; ?>", distance: new String($("#select_distance option:selected").text()), fromafar: new String($("#cb_fromafar").is(':checked')) };

        resl = $.getJSON('MA_getfollowers.php', qopts, function(result) {
            //console.log("entering callback, received unfiltered result:");
            //console.log(result);
            working_targetset = result;
            refilterWorkingTargetSet();
            redisplayWorkingTargetSetStatistics();

        } );

        //console.log("exiting retrievefollowerdata, resl=");
        //console.log(resl);
    }


    //****  Page Event handlers
    //Handler for changes in the target area/distance/fromafar
    areachangehandler = function(){
        //console.log("areachangehandler fired");
        if( $("#cb_fromafar").is(':checked')) { $("#cb_area").prop('disabled', true) ; $("#cb_distance").prop('disabled', true) ; }
        if(! $("#cb_fromafar").is(':checked')) { $("#cb_distance").prop('disabled', false) ;  }        
        if($("#cb_distance").is(':checked')) {  $("#cb_fromafar").prop('disabled', false) ; }
        if(! $("#cb_distance").is(':checked')) { $("#cb_fromafar").prop('disabled', true) ; }

        retrievefollowerdata();
        //prev was async, so probably shouldnt do anything else here.
              
    };
    
    //Handler for changes in the targetset filters
    demochangehandler= function(){
        //console.log("demochangehandler fired");

        refilterWorkingTargetSet();
        redisplayWorkingTargetSetStatistics();

    };

    //Handler for the "add target group" button
    addtargetdemogrouphandler = function(){
        //TODO:
        //add the working set to an array of target sets
        //update the "your selected groups" by calling a funtion that gets a text description of each targetset
        //merge the new working set with any previous target sets
        //update the "message will deliver to" section by calling function that gives text desc of merged set       
        //reset the options and re-calculate

    };

    $("document").ready(function(){
        $(".areaoptions").change(areachangehandler);
        $(".demooptions").change(demochangehandler);

        retrievefollowerdata()

    }); //end doc ready handler


</script>