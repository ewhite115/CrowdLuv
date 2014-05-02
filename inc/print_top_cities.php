<?php 

	$cnt=1;
    $topcities= $CL_model->get_top_cities_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);
    //var_dump($topcities);
    

    foreach($topcities as $row){ ?>

        <table class='cldefaulttable' style='width:100%'>
        	<tr>
                <td style='position:relative;width:85%'>
                    <img style='vertical-align:middle;float:left;' src='res/top-heart.png'>
                    <div style='position:fixed;top:12;left:38;color:white;font-size:14px;'>&nbsp;&nbsp;#<?php echo $cnt++;?></div> 
        			&nbsp; &nbsp; <a href='talent_citydemographics.php?city=<?php echo $row["location_fbname"];?>'><?php echo $row["location_fbname"];?></a></td>
        		<td> 
                    Score: <?php echo $row["city_score"];?><br>
                    <?php echo $row['count(location_fbname)'];?> followers<br>
                    <?php echo $CL_model->calculate_new_followers_for_talent_in_city($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'], $row['location_fbname'], $nfinterval);?> new 

                </td>
        	</tr>
        	</table>

    <?php }  ?>