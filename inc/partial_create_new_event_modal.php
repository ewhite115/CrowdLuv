




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
                <label for="location-string">Location:</label>
                <input name="location-string" type="text"><br>
                <label for="more-info-url">Website for Details:</label>
                <input name="more-info-url" type="url"><br>

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
                $('#CL-form-add-event-status').text("Error creating event: ");
                $('#submit-new-event-link').attr('disabled', 'false');
            })
            .always(function(response){

            });
        
    }


    function onCancelCreateNewEvent(){
        $('#CL_fullpage_transparentscreen').hide();
        $('#CL-modal-add-event').hide();

    }

</script>
