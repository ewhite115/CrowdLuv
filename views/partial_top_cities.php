<?php 

	$cnt=1;
    $topcities= $CL_model->get_top_cities_for_talent($clRequestInformation->getActiveManagedBrand()['crowdluv_tid']);
    //var_dump($topcities);
    

    foreach($topcities as $row){ ?>

       <div class="row crowdluvsection clwhitebg cl_grayborder">
        	<div class="col-xs-2  ">
                <img class="img-responsive" src='res/top-heart.png'>           
                <div style='position:absolute;top:9px;left:25px;color:white;font-size:14px;'><p><b>#<?php echo $cnt++;?></b></p></div>
    		</div>
            <div class="col-xs-6 text-left">
                <p><a href='talent_citydemographics.php?city=<?php echo $row["location_fbname"];?>'><?php echo $row["location_fbname"];?></a></p>
                <p class="clredtext"><b><?php $nfcnt = $CL_model->calculate_new_followers_for_talent_in_city($clRequestInformation->getActiveManagedBrand()['crowdluv_tid'], $row['location_fbname'], $nfinterval); if($nfcnt) echo " ++ " . $nfcnt . " new followers"?></b></p>
    		</div>
            <div class="col-xs-4 text-left">
                <p>Score: <?php echo $row["city_score"];?></p>
                <p><?php echo $row['count(location_fbname)'];?> followers </p>
            </div>
            
        	
        </div>

    <?php }  ?>