    

    <?php

        $preferenceList = [
            'Major Announcements' => 'major_announcements',
            'General Announcements' => 'general_announcements',
            'New Events in my Area' => 'new_event_my_area',
            'New Question Posted' => 'new_question'

        ];

    ?>

    <div class="row cl-follower-talent-preferences">
      <div class="col-xs-7 text-left">
        <h2>&nbsp; </h2>
      </div>
      <div class="col-xs-2 text-center">
        <h2>(mobile)</h2>
      </div>
      <div class="col-xs-2 text-center">
        <h2>(email)</h2>
      </div>
    </div>

    <?php foreach($preferenceList as $prefDesc => $prefString){ ?>
    <div class="row cl-follower-talent-preferences">
      <div class="col-xs-7 text-left">
            <h2><?= $prefDesc;?></h2>
      </div>
      <div class="col-xs-2 text-center">
        <input   type="checkbox" 
                    <?php if($targetTalentPreferences['allow_sms_' . $prefString]) echo " checked " ?>
                    onclick='contact_preference_change_handler(<?php echo $targetTalentPreferences['crowdluv_tid'];?>, "allow_sms_<?= $prefString;?>", this.checked);'>
        </input>
        
      </div>
      <div class="col-xs-2 text-center">
        <input type="checkbox" 
                    <?php if($targetTalentPreferences['allow_email_' . $prefString]) echo " checked " ?>
                    onclick='contact_preference_change_handler(<?php echo $targetTalentPreferences['crowdluv_tid'];?>, "allow_email_<?= $prefString;?>", this.checked);'>
        </input>
      </div>
    </div>
    <?php } ?>

    <p> How long would you travel to see <?php echo $targetTalentPreferences['fb_page_name'];?> ?
        <input  data-crowdluv_tid="<?php echo $targetTalentPreferences['crowdluv_tid'];?>" 
                class="txt_will_travel_time" 
                type="text" 
                size="3" 
                value="<?php echo $targetTalentPreferences['will_travel_time'];?>" /> minutes
        
    </p>  






<script>


    function contact_preference_change_handler(crowdluv_tid, prefname, prefval){

        if(prefval=="on") prefval = 1;
        else if(prefval=="off") prefval = 0;

        console.log("contact pre change handler called:" + crowdluv_tid + ", " + prefname + ", " + prefval);

        var qopts = { 
            crowdluv_tid: crowdluv_tid, 
            prefname: prefname, 
            prefval: prefval
         };
         console.log(qopts);

        resl = $.getJSON('ajax_updatefollowerprefs_fortalent.php', qopts, function(result) {
            console.log("entering callback, received unfiltered result:"); console.log(result);
            
            if(result.prefname == "still_following" && result.result=="1"){
                 //$("#cltrow" + crowdluv_tid).hide(1000);
                 //$("#cltoptsrow" + crowdluv_tid).hide(1000);
            }

        });
        console.log("json call resl="); console.log(resl);



    }
</script>