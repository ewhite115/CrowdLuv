<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/config.php"); 
    include(ROOT_PATH . 'inc/header.php');


    if(! $fb_user) { echo "user is null"; exit;  } 
    if(! $fb_user_pages) { echo "fb_user_pages is null";   }
    //if(! isset($_GET['crowdluv_tid'])) {echo "no crowdluv_tid passed in"; exit;  }
    if(!isset($CL_ACTIVE_MANAGED_TALENT)) {echo "no active talent set"; exit;}

    $topcities=get_top_cities_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);


?>

 
    <div class="crowdluvsection" style="text-align:justify;">
        <div style="display:inline-block">
            <h1>Follower Map</h1>
            <p></p>           
        </div>
    </div>


    <div class="centerdiv  clwhitebg" style="width:95%; padding: 5px; ">
        &nbsp;&nbsp;&nbsp;
        <input type='checkbox' checked id="cb_showheatmap" /> Show Heatmap &nbsp;&nbsp;&nbsp;
        <input type='checkbox' checked id="cb_showmarkers" /> Show Markers &nbsp;&nbsp;&nbsp; 
        <input type='checkbox' checked id="cb_dissipating" /> Dissipating &nbsp;&nbsp;&nbsp;
        <input type="text" id="txt_radius" size="2" value="45" /> Radius<br> 
        <div id="CL_topfollower_googlemap"  />  

    </div>

  <br><br>



<script type="text/javascript">
      
    var geocoder;
    var image = 'res/top-heart-mapmarker.png';
    var map;
    var heatmapdata = new Array();
    var heatmap= new google.maps.visualization.HeatmapLayer({data: heatmapdata});
    var topcitymarkers = new Array();

    function initializeMap() {

        geocoder = new google.maps.Geocoder();
        
        var mapOptions = {
          center: new google.maps.LatLng(-87.629798, 41.878114),
          zoom: 2,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("CL_topfollower_googlemap"), mapOptions);
        
        <?php 
            // $topcities=get_top_cities_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);
            foreach($topcities as $row){
                echo 'addTopCity("' . $row["location_fbname"] . '", "' . $row['count(location_fbname)'] . '");' ;
            }
        ?>
        setCenter("<?php echo $topcities[0]['location_fbname'];?>");   


    }

   function addTopCity(address, followercount) {
        
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {                

                heatmapdata.push({ location : results[0].geometry.location, weight : parseInt(followercount)});
                //console.log("added to heatmapdata:"); console.log(heatmapdata);
                heatmap.setMap(null);
                heatmap = new google.maps.visualization.HeatmapLayer({data: heatmapdata});
                heatmap.setMap(map);
                changeRadius(45);
                
                var marker = new google.maps.Marker({
                    map: map,
                    icon: image,
                    position: results[0].geometry.location,
                    title: address
                });
                topcitymarkers.push(marker);
                var contentString = address + "<br> " + followercount + " followers";
                var infowindow = new google.maps.InfoWindow({content: contentString});
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map,marker);
                });

            } else {
            console.log("A geocode was not successful for the following reason: " + status);
            }
        });
    }

   function setCenter(address) {        
        geocoder.geocode( { 'address': address}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
          } else {
            console.log("Geocode was not successful for the following reason: " + status);
          }
        });
    }

    
    
    function changeRadius(radius) {
      //heatmap.setOptions({radius: heatmap.get('radius') ? null : radius});
      heatmap.setOptions({ radius: radius});
    }

    $(document).ready(function(){
        initializeMap();
        $("#cb_showheatmap").change(function() {
            //console.log("toggleheatmap fired");console.log(heatmap);
            heatmap.setMap(heatmap.getMap() ? null : map);
        });

        $("#cb_showmarkers").change(function() {
            //console.log("togglecitymarkers fired");//console.log(heatmap);
            for(var i=0;i<topcitymarkers.length;i++) { topcitymarkers[i].setMap(topcitymarkers[i].getMap() ? null : map);}
        });

        $("#txt_radius").change(function() {
            heatmap.setOptions({radius: parseInt($("#txt_radius").val())});
        });

        $("#cb_dissipating").change(function() {
            heatmap.setOptions({dissipating: $("#cb_dissipating").is(':checked') });
        });



    });

</script>





<?php include(ROOT_PATH . 'inc/footer.php') ?>