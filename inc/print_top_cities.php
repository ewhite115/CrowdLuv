<?php 

	$cnt=1;
    $topcities= $CL_model->get_top_cities_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);
    //var_dump($topcities);
    

    foreach($topcities as $row){ ?>

       <div class="row crowdluvsection clwhitebg cl_grayborder">
        	<div class="col-xs-1">
                <img style='vertical-align:middle;float:left;' src='res/top-heart.png'>
                <div style='position:fixed;top:12;left:38;color:white;font-size:14px;'>&nbsp;&nbsp;#<?php echo $cnt++;?></div> 
    		</div>
            <div class="col-xs-7 ">
                &nbsp; &nbsp; <a href='talent_citydemographics.php?city=<?php echo $row["location_fbname"];?>'><?php echo $row["location_fbname"];?></a>
    		</div>
            <div class="col-xs-4 ">
                Score: <?php echo $row["city_score"];?><br>
                <?php echo $row['count(location_fbname)'];?> followers<br>
                <?php echo $CL_model->calculate_new_followers_for_talent_in_city($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'], $row['location_fbname'], $nfinterval);?> new 
            </div>
            
        	
        </div>

    <?php }  ?>