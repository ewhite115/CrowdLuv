
    <div class="centerdiv  clwhitebg" style="width:95%; padding: 5px; ">
        &nbsp;&nbsp;&nbsp;
        <input type='checkbox' checked id="cb_showheatmap" /> Show Heatmap &nbsp;&nbsp;&nbsp;
        <input type='checkbox' checked id="cb_showmarkers" /> Show Markers &nbsp;&nbsp;&nbsp; 
        <input type='checkbox' checked id="cb_dissipating" /> Dissipating &nbsp;&nbsp;&nbsp;
        <input type="text" id="txt_radius" size="2" value="45" /> Radius<br> 
        <div id="CL_topfollower_googlemap"  />  

    </div>

  <br><br>


<script src="<?php echo BASE_URL;?>scripts/cl_followermap.js"></script> 

<script type="text/javascript">        

    $(document).ready(function(){
        initializeCLMap();        
        <?php  foreach($topcities as $row){ ?>  addTopCityToCLMap("<?php echo $row['location_fbname'];?>", "<?php echo $row['count(location_fbname)'];?>");  <?php  }  ?>    
        setCLMapCenter("<?php echo $topcities[0]['location_fbname'];?>");   
        
        //Set UI control handlers
        $("#cb_showheatmap").change(toggleCLHeatmap);
        $("#cb_showmarkers").change(toggleCLMarkers);
        $("#cb_dissipating").change(toggleCLMapDissipating);
        $("#txt_radius").change(function() {heatmap.setOptions({radius: parseInt($("#txt_radius").val())});});    

    });

</script>
