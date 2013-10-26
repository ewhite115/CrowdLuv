
     
    var geocoder;
    var image = 'res/top-heart-mapmarker.png';
    var map;
    var heatmapdata = new Array();
    var heatmap= new google.maps.visualization.HeatmapLayer({data: heatmapdata});
    var topcitymarkers = new Array();
    var infowindowlist = new Array();
    var dissipating=true;

    function initializeCLMap() {

        geocoder = new google.maps.Geocoder();
        
        var mapOptions = {
          center: new google.maps.LatLng(-87.629798, 41.878114),
          zoom: 2,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("CL_topfollower_googlemap"), mapOptions);           

    }

   function addTopCityToCLMap(address, followercount) {
        
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
                infowindowlist.push(infowindow);
                google.maps.event.addListener(marker, 'click', function() {
                    //TOO: close the infowindow for all other markers
                    for(var i=0; i< infowindowlist.length;i++){ infowindowlist[i].close(); }
                
                    infowindow.open(map,marker);
                });

            } else {
            console.log("A geocode was not successful for the following reason: " + status);
            }
        });
    }


   function setCLMapCenter(address) {        
        geocoder.geocode( { 'address': address}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
          } else {
            console.log("Geocode was not successful for the following reason: " + status);
          }
        });
    }

    toggleCLHeatmap = function() {heatmap.setMap(heatmap.getMap() ? null : map);  };
    toggleCLMarkers = function() {for(var i=0;i<topcitymarkers.length;i++) { topcitymarkers[i].setMap(topcitymarkers[i].getMap() ? null : map);}};
    toggleCLMapDissipating = function() {heatmap.setOptions({dissipating: dissipating = !dissipating }); };

    function changeRadius(radius) {
      //heatmap.setOptions({radius: heatmap.get('radius') ? null : radius});
      heatmap.setOptions({ radius: radius});
    }

    