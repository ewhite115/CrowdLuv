<?php 

	$cnt=1;
    $topcities= $CL_model->get_top_cities_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);
    foreach($topcities as $row){
        echo "<table class='cldefaulttable' style='width:100%'>
        	<tr>
        		<td style='position:relative;width:85%'>
        			<img style='vertical-align:middle;float:left;' src='res/top-heart.png'>

        			<div style='position:fixed;top:12;left:38;color:white;font-size:14px;'>&nbsp;&nbsp;# " . $cnt++ . "</div> 
        			&nbsp; &nbsp; <a href='talent_messagefollowers.php?city=" . $row["location_fbname"] . "'>" . $row["location_fbname"] ."</a></td>
        		<td>" . $row['count(location_fbname)'] . " followers</td>
        	</tr>
        	</table>";
    }
    
 ?>