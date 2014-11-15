




<!-- ***  Modal for adding an event -->
<div hidden id="CL-modal-add-event" class="CL_modal text-center crowdluvsection">
    <h1 class="cl-textcolor-standout">Add a <?= $CL_CUR_TGT_TALENT['fb_page_name'];?> Event</h1>



    <?php if(isset($CL_LOGGEDIN_USER_OBJ)) { ?>
        <div>
            <form id="CL-form-add-event">
                <input type="hidden" name="created-by-crowdluv-uid" value="<?= $CL_LOGGEDIN_USER_UID;?>">
                <input type="hidden" name="created-for-crowdluv-tid" value="<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?>">
                <label for="type">Type:</label>
                <select name="type">
                  <option value="performance">Performance/Appearance</option>
                  <option value="release">New Release</option>
                  <option value="other">Other</option>
                </select><br>
                <label for="title">Title:</label>
                <input name="title" type="text"><br>
                <label for="description">Description:</label>
                <input name="description" type="textarea"><br>
                <label for="start-date">Start Date:</label>
                <input name="start-date" type="date"><br>
                <label for="start-time">Start Time:</label>
                <input name="start-time" type="time"><br>
                <input type="hidden" name="duration" value="1 hour">
 
                <label for="more-info-url">Website for Event Details</label><br>
                <input name="more-info-url" type="url"><br>

                <label>Event Location</label><br>
                <label for="location-venue-name">Lookup Venue Name:</label>
                <input name="location-venue-name" type="text"><br>

                <label for="location-street-address">Street Address:</label>
                <input name="location-street-address" type="text"><br>

                <label for="location-city">City:</label>
                <input name="location-city" type="text"><br>

                <label for="location-state">State:</label>
                <input name="location-state" type="text"><br>

                <label for="location-country">Country:</label>
                <input name="location-country" type="text"><br>

                <label for="location-zip">Zip:</label>
                <input name="location-zip" type="text"><br>

                <label for="location-latitude">Latitude:</label>
                <input name="location-latitude" type="text"><br>

                <label for="location-longitude">Longitude:</label>
                <input name="location-longitude" type="text"><br>

                <input type="hidden" name="location-fb-pid">

 

            </form>
            <p id="CL-form-add-event-status" class="cl-textcolor-standout"> </p>

        </div>

        <button id="submit-new-event-button" class="cl-button-standout" onclick="onSubmitCreateNewEvent();return false;">
            Create
        </button>

    <?php } else{ ?>
        <br>
        <p> Sign in to create events for your favorite acts.</p>
        <br>
        <p> Creating events on CrowdLuv lets you help your favorite acts spread the word about their upcoming performances, appearances, new releases, and other significant happenings.</p>
        <br>
        <p> If other fans share your events, you will get LuvPoints and increase your fan rank</p>


    <?php } ?>



    <button id="cancel-new-event-button" onclick="onCancelCreateNewEvent();return false;">
        Cancel
    </button>

</div>





<script type="text/javascript">
    
    
    /**
     * [onSubmitCreateNewEvent Handler for the "Submit New Event" Button]
     * @return {[type]} [description]
     */
    function onSubmitCreateNewEvent(){
     
        $('#submit-new-event-button').attr('disabled', 'true');
        $('#submit-new-event-button').text('Creating Event....');

        //TODO:  add validation of the new-event form

        //Submit the input data to the ajax handler for new events
        $.post( "ajax_handle_post.php", "ajaxPostType=createNewEvent&" + $( "#CL-form-add-event" ).serialize(),
            function(response, status, xhr){

                console.log("ajax_handle_post for new event creation:");
                console.log(response);

                if(response.result == "Validation Failed"){
                    
                    $('#CL-form-add-event-status').text(response.validation_error);
                    $('#submit-new-event-button').removeAttr('disabled');
                    $('#submit-new-event-button').text('Create Event');

                }
                else{
                    $('#CL-form-add-event-status').text("Success!").delay(1000);
                    $('#CL_fullpage_transparentscreen').hide();
                    $('#CL-modal-add-event').hide();
                    $('#submit-new-event-button').removeAttr('disabled').text('Create Event');
                }
                
            }, "json")
            .done(function(response){

            })
            .fail(function(response){
                console.log("failure on ajax_handle_post for new event creation");
                console.log(response);
                $('#CL-form-add-event-status').text("Error creating event: ");
                $('#submit-new-event-button').removeAttr('disabled').text('Create Event');
            })
            .always(function(response){

            });
        
    }


    function onCancelCreateNewEvent(){
        $('#CL_fullpage_transparentscreen').hide();
        $('#CL-modal-add-event').hide();

    }



    $(document).ready(function(){  

        var tokenUrl = "";

        $("[name='location-venue-name']").autocomplete({
            //source: ['Heldrich', 'The Heldrich', 'Heldrich2', 'Heldrich3']
            source: function(request, add) {
                        $this = $(this)
                        // Call out to the Graph API for the friends list
                        tokenUrl = "https://graph.facebook.com/search?q=" + request.term +"&type=place&fields=location,name,id,checkins&limit=20&access_token=<?php echo $facebookSession->getToken();?>";// + "&callback=?"
                        $.ajax({
                            url: tokenUrl,
                            dataType: "jsonp",
                            success: function(results){
                                console.log("success on autocomplete ajax call");
                                // Filter the results and return a label/value object array  
                                var formatted = [];
                                for(var i = 0; i< results.data.length; i++) {
                                    //if (results.data[i].name.toLowerCase().indexOf($("[name='location-fb']").val().toLowerCase()) >= 0)
                                    formatted.push({
                                        label: results.data[i].name + " - " + results.data[i].location.city + ", " + results.data[i].location.state + "," + results.data[i].location.country,
                                        value: results.data[i].name,                                        
                                        fb_pid: results.data[i].id,
                                        location: results.data[i].location

                                    })
                                }
                                add(formatted);
                            }
                        });
                    },
                    select: function(event, ui) {
                        // Fill in the input fields
                        //$("[name='location-venue-name']").val(ui.item.val);
                        $("[name='location-street-address']").val(ui.item.location.street);
                        $("[name='location-city']").val(ui.item.location.city);
                        $("[name='location-state']").val(ui.item.location.state);
                        $("[name='location-country']").val(ui.item.location.country);
                        $("[name='location-zip']").val(ui.item.location.zip);
                        $("[name='location-latitude']").val(ui.item.location.latitude);
                        $("[name='location-longitude']").val(ui.item.location.longitude);
                        $("[name='location-fb-pid']").val(ui.item.fb_pid);
                        

                        // Prevent the value from being inserted in "#name"
                        //return false;
                    },
                    minLength: 3

        });

    });

</script>
