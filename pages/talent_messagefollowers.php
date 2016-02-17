<?php 
//require_once "../inc/cl_bootstrap.php"; 

$clResponseInformation->clCiteSection = "talent";


if(! isset($CL_ACTIVE_MANAGED_TALENT)) {echo "no active talent set"; exit;}
if(! isset($_GET['city'])) {echo "no city passed in"; exit;  }
$city=$_GET['city'];
$citystats= $CL_model->get_city_stats_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'], $city, 5);



include(ROOT_PATH . 'views/partial_cl_html_leader.php'); 

?>

 

<script>

    //An array of objects called 
    //targetsets  contain info about criteria and list of CL followers included based
    //on that criteria
    var targetsets = new Array();
    var working_targetset = new Object();
 

    //message_audience object contains a targetset of all the CL followers who will ultimately 
    //receive the message.  The "This message will deliver to" section will summarize contents of
    //this object and this object will be sent to server 
    var message_audience = new Object();
    message_audience.followers = new Array();

    //When target area changes:
        //call ajax to get a new working set
        //do the things mentioned below for when filters change

    //When any of the target filters changes:
        //Re-calculate the working target set by 'filter'ing against the criteria 
            //  set an "included" flag to false
            //male/female filtering shoudl be based on combined values (but keep separate rows to show stats)

        //Re-calculate and display the statistics for each category

    //When talent clicks add:
        //add this working set to the array of target sets
        //also, merge the new working set with any previous target sets
        //update the "your selected groups" by calling a funtion that gets a text description of each targetset
        //update the "message will deliver to" section by calling function that gives text desc of merged set       
    
 
    //Page load
    $(document).ready(function(){
        console.log("talent_messagefollowers - entering doc.ready()");
        resetDemographicControls();
        resetWorkingTargetSet();
        
        retrievefollowerdata();

        $("#modal_targetsetbuilder").hide();
        $(".CL_transparentscreen").hide();


        //Set handlers
        $(".areaoptions").change(areachangehandler);
        $(".demooptions").change(handler_demographicchanges);
        $("#btn_demoreset").click(resetDemographicControls);
        $("#btn_addtargetdemogroup").click( btn_addtargetdemogroup_clickhandler);
        $("#btn_addATargetGroup").click( function(){ 
            $(".CL_transparentscreen").show();
            $("#modal_targetsetbuilder").show();  
        });

    }); //end doc ready handler


    
    function retrievefollowerdata() {      
        console.log("entering retrievefollowerdata");
        //ajax call to get back a JSON object of *all* the followers in the targeted area + radius
        
        $(".CL_transparentscreen").show();
        $("#modal_retrievinginfo").show();  
 
        var qopts = { 
            crowdluv_tid: "<?php echo $CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'];?>", 
            crossDomain: true,
            city: working_targetset.areaname, 
            distance: working_targetset.distance,
            fromafar: new String(working_targetset.fromafar) 
         };

        resl = $.getJSON('<?php echo CLADDR;?>ajax_getfollowers.php', qopts, function(result) {
            console.log("entering callback, received unfiltered result:"); console.log(result);
            working_targetset.followers = result;
            recalculateTargetSet(working_targetset);
            displayWorkingTargetSetStatistics();

            //$(".CL_transparentscreen").hide();
            $("#modal_retrievinginfo").hide();      

        } );

        console.log("exiting retrievefollowerdata, resl="); console.log(resl);
    }

    function displayWorkingTargetSetStatistics(){

        $("#lbl_areacount").text(working_targetset.areacount);
        $("#lbl_femalecount").text(working_targetset.femalecount);
        $("#lbl_malecount").text(working_targetset.malecount);
        $("#lbl_relationshipcount").text(working_targetset.relationshipcount);
        $("#lbl_singlecount").text(working_targetset.singlecount);
        $("#lbl_agerangecount").text(working_targetset.agerangecount);
        $("#lbl_signupanydatecount").text(working_targetset.signupanydatescount);
        $("#lbl_signedup30count").text(working_targetset.signup30count);
        $("#lbl_signedup90count").text(working_targetset.signup90count);
        $("#lbl_signedup365count").text(working_targetset.signup365count);

    }

    //****  Page Event handlers
    //Handler for changes in the target area/distance/fromafar
    areachangehandler = function(){
        //console.log("areachangehandler fired");
        if( $("#cb_fromafar").is(':checked')) { $("#cb_area").prop('disabled', true) ; $("#cb_distance").prop('disabled', true) ; }
        if(! $("#cb_fromafar").is(':checked')) { $("#cb_distance").prop('disabled', false) ;  }        
        if($("#cb_distance").is(':checked')) {  $("#cb_fromafar").prop('disabled', false) ; }
        if(! $("#cb_distance").is(':checked')) { $("#cb_fromafar").prop('disabled', true) ; }

        //$(".CL_transparentscreen").show();
        //$("#modal_retrievinginfo").show();

        retrievefollowerdata();
        //prev was async, so probably shouldnt do anything else here.
              
    };
    

    function setWorkingTargetSetDemoOptions(){

        //Set the filter values on the working_targetset object to match the interface selections
        working_targetset.areaname = "<?php echo $city;?>"; //TODO:  make this dynaimc JS allow talent to choose city on page
        if(! $("#cb_distance").is(':checked')) working_targetset.distance=0;
        else working_targetset.distance = new String($("#select_distance option:selected").text());
        working_targetset.fromafar = $("#cb_fromafar").is(':checked') ;

        working_targetset.incl_female= $("#cb_female").is(':checked');
        working_targetset.incl_male= $("#cb_male").is(':checked');
        working_targetset.incl_relationship= $("#cb_relationship").is(':checked');
        working_targetset.incl_single= $("#cb_single").is(':checked');
        working_targetset.incl_agerange= $("#cb_agerange").is(':checked');
        working_targetset.min_age= $("#txt_min_age").val();
        working_targetset.max_age= $("#txt_max_age").val();
        working_targetset.incl_signup30= $("#cb_30days").is(':checked');
        working_targetset.incl_signup90= $("#cb_90days").is(':checked');
        working_targetset.incl_signup365= $("#cb_365days").is(':checked');

    }
    
    //Handler for changes in the demographic filters
    handler_demographicchanges = function(){
        //console.log("demochangehandler fired");

        //rules on the age range
        if( $("#cb_agerange").is(':checked')) { $("#txt_min_age").prop('disabled', false) ; $("#txt_max_age").prop('disabled', false) ; }
        if(! $("#cb_agerange").is(':checked')) { 
            $("#txt_min_age").prop('disabled', true) ;$("#txt_min_age").prop('value', 0) ;
            $("#txt_max_age").prop('disabled', true) ; $("#txt_max_age").prop('value', 110) ;
        }

        setWorkingTargetSetDemoOptions();
        recalculateTargetSet(working_targetset);
        displayWorkingTargetSetStatistics();

    };

    //Handler for the "add this target group" button
    btn_addtargetdemogroup_clickhandler = function(){

        $("#modal_targetsetbuilder").hide();

        //add the working set to an array of target sets
        targetsets.push(working_targetset);

        //Update the "Your target groups" section of the page with a description of 
        //this added target group
        //console.log("About to set target groups text. targetsets:"); console.log(targetsets);
        if(targetsets.length == 1) $("#p_targetgroups").html("<br><b>&nbsp;(Group 1)</b> - " + getTextDescriptionOfTargetSet(working_targetset) + "<br>");
        else $("#p_targetgroups").html($("#p_targetgroups").html() + "+<b>(Group " + (targetsets.length) + "</b>) - " + getTextDescriptionOfTargetSet(working_targetset) + "<br>");
      
        //merge the new working set with any previous target sets and
        //update the "message will deliver to" section by calling function that gives text desc of merged set       
        calculateMessageAudience();
        $("#p_messageaudience").html( getTextDescriptionOfMessageAudience(message_audience) + "<br>");
        
        //reset the options and re-retrieve follower data
        resetDemographicControls();
        resetWorkingTargetSet()
        retrievefollowerdata();

        $("#btn_addATargetGroup").text("Add another target group");
        $(".CL_transparentscreen").hide();
        
    };


    //Handler for "reset filters" button
    resetDemographicControls = function(){
        //TODO:  reset the checkmarks on the filters to default values

    };


   function resetWorkingTargetSet(){

        working_targetset= new Object();
        working_targetset.followers = new Array();    
        setWorkingTargetSetDemoOptions();

    }


    //This function will compile a "messageaudience" which is just 
    //a target set created by merging all of the "included" followers from
    //the set of targetsets/  
    function calculateMessageAudience(){
        console.log("entering calcmsgaud");
        message_audience = new Object();
        message_audience.followers = new Array();

        for(var i=0; i < targetsets.length;i++){  //loop through each target set
            for(var j=0; j < targetsets[i].followers.length;j++){  //loop through each follower in the target set
                if(targetsets[i].followers[j].included){
                    //we've found a follower who's included - if they arent a dup, add to MA
                    var dup=0;
                    for(var k=0;k <message_audience.followers.length;k++){
                        if(message_audience.followers[k]['crowdluv_uid'] == targetsets[i].followers[j]['crowdluv_uid'])  dup=1;
                    }
                    if(!dup) {console.log("pushing to ma");message_audience.followers.push(targetsets[i].followers[j]);}

                }
            }
        }
        calculateTargetSetStatistics(message_audience);

    }


    //Target-set functions
    function recalculateTargetSet(tgtset){
        refilterTargetSet(tgtset);
        calculateTargetSetStatistics(tgtset);
    }

    function refilterTargetSet(tgtset){
 
        //Re-calculate the working target set by 'filter'ing against the criteria 
        //Start by assuming everyone is included, then progressively exclude
        //them if they dont meet the current criteria
        for (var i = 0; i < tgtset.followers.length; i++) {
            //Default includsion to true
            tgtset.followers[i]['included'] = true;
            //Exclude if male/female
            if( tgtset.followers[i]['gender'] == "female" & ! tgtset.incl_female) tgtset.followers[i]['included'] = false ;
            if( tgtset.followers[i]['gender'] == "male" & ! tgtset.incl_male) tgtset.followers[i]['included'] = false ;
            //Exclude those in reltionship
            if( ! tgtset.incl_relationship &
                  ( tgtset.followers[i]['fb_relationship_status'] == "In a relationship" ||
                    tgtset.followers[i]['fb_relationship_status'] == "Engaged" ||
                    tgtset.followers[i]['fb_relationship_status'] == "Married" )) tgtset.followers[i]['included'] = false ;
            //Exclude single, divorced
            if(! tgtset.incl_single & 
                (tgtset.followers[i]['fb_relationship_status'] == "Single" ||
                 tgtset.followers[i]['fb_relationship_status'] == "Divorced" )) tgtset.followers[i]['included'] = false ;

            //Exclude if an age range is specified and they dont meet it
            var age = ((Date.now() - new Date(tgtset.followers[i]['birthdate'])) / (31557600000));
            //var min_age = $("#txt_min_age").val();
            //var max_age = $("#txt_max_age").val(); 
            //console.log("age" + age + " max_age:"+ max_age + " min_age: " + min_age);
            if(tgtset.incl_agerange & (age < tgtset.min_age || age > tgtset.max_age)) tgtset.followers[i]['included'] = false ;   

            //Exclude on signup date
            var daysfromsignup = ((Date.now() - new Date(tgtset.followers[i]['signupdate'])) / (31557600000)) * 365;
            //console.log("daysfromsignup:"+ daysfromsignup);
            if(tgtset.incl_signup30 & (daysfromsignup > 30)) tgtset.followers[i]['included'] = false ;               
            if(tgtset.incl_signup90 & (daysfromsignup > 90)) tgtset.followers[i]['included'] = false ;               
            if(tgtset.incl_signup365 & (daysfromsignup > 365)) tgtset.followers[i]['included'] = false ;               

        }//end for


        //console.log("Outputting re-filtered tgtset.followers:");console.log(tgtset.followers);


        //try using this jquery later for easier loop
        /*$.each(yourArray, function(index, value) {
          // do your stuff here
        });*/
    }
  
    function calculateTargetSetStatistics(tgtset){

       //Determine how many of each type are still "included" in the working target set and 
        //display that ifo in the left column of the table
        //console.log("entering redisplay...()  workingtarget=");
        //console.log(working_targetset.followers);
        tgtset.areacount=0;
        tgtset.extareacount=0;
        tgtset.extareaafarcount=0;
        tgtset.femalecount=0;
        tgtset.malecount=0;
        tgtset.relationshipcount=0;
        tgtset.singlecount=0;
        tgtset.agerangecount=0;
        tgtset.signupanydatescount=0;
        tgtset.signup30count=0;
        tgtset.signup90count=0;
        tgtset.signup365count=0;


       for (var i = 0; i < tgtset.followers.length; i++) {
            //if this follower is excluded from the target set, skip over them
            if( ! tgtset.followers[i]['included']) continue;

            tgtset.areacount++;
            tgtset.extareacount++;

            if( tgtset.followers[i]['gender'] == "female") tgtset.femalecount++ ;
            if( tgtset.followers[i]['gender'] == "male") tgtset.malecount++ ;
            
            if(   ( tgtset.followers[i]['fb_relationship_status'] == "In a relationship" ||
                    tgtset.followers[i]['fb_relationship_status'] == "Engaged" ||
                    tgtset.followers[i]['fb_relationship_status'] == "Married" )) tgtset.relationshipcount++;

            if( (tgtset.followers[i]['fb_relationship_status'] == "Single" ||
                 tgtset.followers[i]['fb_relationship_status'] == "Divorced" )) tgtset.singlecount++ ;
            
            var age = ((Date.now() - new Date(tgtset.followers[i]['birthdate'])) / (31557600000));
            //var min_age = $("#txt_min_age").val();
            //var max_age = $("#txt_max_age").val(); 
            //console.log("age" + age + " max_age:"+ max_age + " min_age: " + min_age);
            if((age >= tgtset.min_age & age <= tgtset.max_age)) tgtset.agerangecount++ ;   

            //signup date
            var daysfromsignup = ((Date.now() - new Date(tgtset.followers[i]['signupdate'])) / (31557600000)) * 365;
            //console.log("daysfromsignup:"+ daysfromsignup);
            if((daysfromsignup < 31)) tgtset.signup30count++ ;               
            if((daysfromsignup < 91)) tgtset.signup90count++ ;               
            if((daysfromsignup < 366)) tgtset.signup365count++;               
            tgtset.signupanydatescount++;

        }//end for
        //console.log("finished counting.. female:" + femalecount + "; male:" + malecount + "; relationship:" + relationshipcount + "; single:"+singlecount);
 
    }


    function getTextDescriptionOfTargetSet(tgtset){
    
        var descr = "";

        descr =  "All followers ";
        if(tgtset.fromafar) descr = descr + "who are willing to travel from "
        if(tgtset.distance > 0) descr = descr + "within " + tgtset.distance + " miles ";
        if(tgtset.areaname) descr = descr + "from " + tgtset.areaname;
        
        

        var crit= new Array();

        if(tgtset.incl_female & tgtset.incl_male ) ;// crit = crit + " either female or male";
        else if(tgtset.incl_female) crit.push("female");
        else if(tgtset.incl_male) crit.push("male");
        if(tgtset.incl_single & tgtset.incl_relationship) ;// crit = crit + ", any relationship status";
        else if(tgtset.incl_single) crit.push("single/divorced");
        else if(tgtset.incl_relationship) crit.push("in a relationship/engaged/married");
        if(tgtset.incl_agerange) crit.push("between the ages of " + tgtset.min_age + " - " + tgtset.max_age);
        if(tgtset.incl_signup30) crit.push("who have signed up in the past 30 days");
        if(tgtset.incl_signup90) crit.push("who have signed up in the past 90 days");
        if(tgtset.incl_signup365) crit.push("who have signed up in the past year");

        if(crit.length>0) descr = descr + ", who are " + crit[0];
        var i=0;
        for(i=1; i < crit.length ; i++ ) {descr = descr + " and " + crit[i];  }

        descr = descr + " <b>(" + tgtset.areacount + " followers total)</b>";
        return descr;
    }



    function getTextDescriptionOfMessageAudience(tgtset){
    
        var descr = "";

        descr = "A total of " + tgtset.areacount + " followers, including:";
        //if(tgtset.areaname) descr = descr + "from " + tgtset.areaname;
        //if(tgtset.distance > 0) descr = descr + " and within " + tgtset.distance + " miles;";
        //if(tgtset.fromafar) descr = descr + " who are willing to travel that far; "

        var crit= new Array();

        crit.push(tgtset.femalecount + " female");
        crit.push(tgtset.malecount + " male");
    
        crit.push(tgtset.singlecount + " single/divorced");
        crit.push(tgtset.relationshipcount + " in a relationship/engaged/married");
        //crit.push(tgtset.agerangecount +" between the ages of " + tgtset.min_age + " - " + tgtset.max_age);
        crit.push(tgtset.signup30count + " who have signed up in the past 30 days");
        crit.push(tgtset.signup90count + " who have signed up in the past 90 days");
        crit.push(tgtset.signup365count + " who have signed up in the past year");

        //if(crit.length>0) descr = descr + ", who are";
        var i=0;
        for(i=0; i < crit.length ; i++ ) {descr = descr + "<br> ----- " + crit[i];  }

        return descr;
    }




</script>


 
    <div class="crowdluvsection"> 
        <h1>Message Followers - <?php echo $CL_ACTIVE_MANAGED_TALENT['fb_page_name'];?><img style="display:inline-block;width:2%;" src='https://graph.facebook.com/<?php echo $CL_ACTIVE_MANAGED_TALENT['fb_pid']; ?>/picture?access_token=<?php echo $facebookSession->getToken();?>'></h1>
        <br> 
    </div>
    
    <br>
 

    <div class="fluid-row">

        <div class="col-xs-4 col-sm-6 crowdluvsection cl_grayborder" style="min-width:300px;">
            <h1>Step 1: Choose your target audience(s)</h1>           
            <p id="p_targetgroups">Add one of more target groups for this message by selecting the demographics you would like to target</p>
            <br>
            <button type="button" id="btn_addATargetGroup">Add a target group</button><br>
            <br>

        </div>
 
    
        <div class="col-xs-4 col-sm-6 crowdluvsection cl_grayborder" style="min-width:300px;">
            <h1>Step 2: Compose your message</h1>
            <p><input type="checkbox" /> Text Message &nbsp;&nbsp; &nbsp;  <input type="checkbox"> Email Message</p>
            <textarea rows='5' cols='25' style="box-sizing:border-box;width:90%;margin-left:auto;margin-right:auto;background-color:#fff;" > </textarea>
     
        </div>
    </div>

    <div class="fluid-row">

        <div class="col-xs-4 col-sm-6 crowdluvsection cl_grayborder" style="min-width:300px;">

            <h1>Step 3: Send your message</h1>

            <h2> The message will deliver to:</h2>
             <p id="p_messageaudience"> You have not selected any target groups yet.</p>
            <br>
            <hr>                
            <div class="crowdluvsection" style="display:inline-block;width:40%;">        
                <h3>Cost to send:</h3>
                 <p2>nnn credits</p2>
            </div>
            <div class="crowdluvsection" style="display:inline-block;width:40%;">        
                <h3>Your current balance</h3>
                 <p>nnn credits (get more)</p2><br><br>
            </div>
            <hr>

            <button type='button'>Send Message</button><br>
            <br>

        </div>

        <div class="col-xs-4 col-sm-6 crowdluvsection cl_grayborder" style="min-width:300px;">
            <p>Text Message History</p>
            <br>
            <h2>This will be the previously sent message #1</h2>
            <p2>Day, time</p2>
            <p2>Selected: statistics on selection</p2>
            <br><br>
            <h2>This will be the previously sent message #2</h2>
            <p2>Day, time</p2>
            <p2>Selected: statistics on selection</p2>
            <br><br>
            <p2><a href="#">See More &gt;</a></p2>

        </div>
        

    </div> 


    <div class="CL_transparentscreen"></div> 
           

    <div class="CL_modal crowdluvsection" id="modal_targetsetbuilder" style="top:5%;">
      <h1>Choose your message audience</h1>
      <p>Create a target group to receive this message</p>
            
        <h2>Select target area / radius</h2>
        <table class='cldefaulttable' style="width:100%;" ><tr><td style="width:15%;"><label id="lbl_areacount"><?php echo $citystats['followercount'];?></label></td><td style="width:85%;"> in <label id="lbl_areaname"><?php echo $city;?></label></td><td><input type='checkbox' checked disabled="true" class="areaoptions" id="cb_area"/></td></tr></table><br>
        <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_extareacount"></label></td><td style="width:85%;">plus all within<select class="areaoptions" id="select_distance" disabled="true"><option value="5">5</option><option value="10">10</option><option value="20">20</option></select>  mile radius </td><td><input type='checkbox' class="areaoptions" id="cb_distance" /></td></tr></table><br>
        <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_extareacountafar"></label></td><td style="width:85%;"> ..who are willing to travel that far</td><td><input type='checkbox' disabled="true" class="areaoptions" id="cb_fromafar" /></td></tr></table><br>

        <h2>Select gender(s) to target in this group</h2>
        <table class='cldefaulttable' style="width:100%;" ><tr><td style="width:15%;"><label id="lbl_femalecount"><?php echo $citystats['female'];?></label></td><td style="width:85%;"> Female</td><td><input type='checkbox' checked class="demooptions" id="cb_female" /></td></tr></table><br>
        <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_malecount"><?php echo $citystats['male'];?></label></td><td style="width:85%;"> Male</td><td><input type='checkbox' checked class="demooptions" id="cb_male" /></td></tr></table><br>
        
        <h2>Select relationship statuses to target in this group</h2>
        <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_relationshipcount"><?php echo $citystats['relationship'];?></label></td><td style="width:85%;"> Relationship, Engaged, Married</td><td><input type='checkbox' checked class="demooptions" id="cb_relationship"/></td></tr></table><br>
        <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_singlecount"><?php echo $citystats['single'];?></label></td><td style="width:85%;"> Single, Divorced</td><td><input type='checkbox' checked class="demooptions" id="cb_single"/></td></tr></table><br>
        
        <h2>Select age range to target in this group (un-check for all ages)</h2> 
        <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_agerangecount"></label></td><td style="width:85%;"> Age Group <input disabled="true" type="text" class="demooptions" id="txt_min_age" size="2" value="0" /> - <input type="text" disabled="true" class="demooptions" id="txt_max_age" size="2" value="110" /> </td><td><input type='checkbox' class="demooptions" id="cb_agerange" /></td></tr></table><br>              

        <h2>Target this group based on signup date</h2>          
        <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_signupanydatecount"></label></td><td style="width:85%;"> Signed up any time</td><td><input type='radio' name="signup" checked class="demooptions" id="cb_signupanydate"/></td></tr></table><br>
        <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_signedup30count"><?php echo $citystats['signedup30'];?></label></td><td style="width:85%;"> signed up w/in 30 days</td><td><input type='radio' name="signup" class="demooptions" id="cb_30days"/></td></tr></table><br>
        <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_signedup90count"><?php echo $citystats['signedup90'];?></label></td><td style="width:85%;"> signed up w/in 90 days</td><td><input type='radio' name="signup" class="demooptions" id="cb_90days" /></td></tr></table><br>
        <table class='cldefaulttable' style="width:100%;"><tr><td style="width:15%;"><label id="lbl_signedup365count"><?php echo $citystats['signedup365'];?></label></td><td style="width:85%;"> signed up w/in 1 year</td><td><input type='radio' name="signup" class="demooptions" id="cb_365days"/></td></tr></table><br>
        <button type="button" id="btn_addtargetdemogroup">Add this target group</button>
        <button type="button" id="btn_demoreset">Reset filters</button>       
    </div> 
    
           
    <div class="CL_modal" id="modal_retrievinginfo"><span>Retrieving follower information....</span></div>
    

 







<?php include(ROOT_PATH . 'views/partial_footer.php') ?>
