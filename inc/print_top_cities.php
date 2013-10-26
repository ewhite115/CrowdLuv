<?php 

	$cnt=1;
    $topcities=get_top_cities_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);
    foreach($topcities as $row){
        echo "<table class='cldefaulttable' style='width:100%'><tr><td style='position:relative;width:85%'><img style='vertical-align:middle;' src='res/top-heart.png'><div style='position:absolute;top:12;left:18;color:white;font-size:14px;'># " . $cnt++ . "</div> &nbsp; &nbsp; <a href='talent_messagefollowers.php?city=" . $row["location_fbname"] . "'>" . $row["location_fbname"] ."</a></td><td>" . $row['count(location_fbname)'] . " followers</td></tr></table>";
    }
    
 ?>