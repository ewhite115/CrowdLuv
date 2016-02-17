




<!-- ***  Modal for adding an question -->
<div hidden id="CL-modal-add-question" class="CL_modal text-center crowdluvsection">
    <h1 class="cl-textcolor-standout">Ask a question about <?= $CL_CUR_TGT_TALENT['fb_page_name'];?> </h1>



    <?php if(isset($CL_LOGGEDIN_USER_OBJ)) { ?>
        <div>
            <form id="CL-form-add-question">
                <input type="hidden" name="created-by-crowdluv-uid" value="<?= $CL_LOGGEDIN_USER_UID;?>">
                <input type="hidden" name="created-for-crowdluv-tid" value="<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?>">
                <label for="title">Title:</label>
                <input name="title" type="text"><br>
                <label for="description">Description:</label>
                <input name="description" type="textarea"><br>
                

            </form>
            <p id="CL-form-add-question-status" class="cl-textcolor-standout"> </p>

        </div>

        <button id="submit-new-question-button" class="cl-button-standout" onclick="onSubmitCreateNewQuestion();return false;">
            Create
        </button>

    <?php } else{ ?>
        <br>
        <p> Sign in to ask a question about your favorite acts.</p>
        <br>
        <p> Creating events on CrowdLuv lets you help your favorite acts spread the word about their upcoming performances, appearances, new releases, and other significant happenings.</p>
        <br>
        <p> If other fans vote up your question, you will get LuvPoints and increase your fan rank</p>

    <?php } ?>


    <button id="cancel-new-question-button" onclick="onCancelCreateNewQuestion();return false;">
        Cancel
    </button>

</div>





<script type="text/javascript">
    
    
    /**
     * [onSubmitCreateNewQuestion Handler for the "Submit New Question" Button]
     * @return {[type]} [description]
     */
    function onSubmitCreateNewQuestion(){
     
        $('#submit-new-question-button').attr('disabled', 'true');
        $('#submit-new-question-button').text('Submitting Question....');

        //TODO:  add validation of the submit-new-question form

        //Submit the input data to the ajax handler for new questions
        //var postData = { formData: $( "#CL-form-add-question" ).serialize() };
        crowdluvAPIPost("createNewQuestion", $( "#CL-form-add-question" ).serialize(),
        //$.post( "ajax_handle_post.php", "ajaxPostType=createNewQuestion&" + $( "#CL-form-add-question" ).serialize(),
            function(response, status, xhr){

                if(response.result == "Validation Failed"){
                    
                    $('#CL-form-add-question-status').text(response.validation_error);
                    $('#submit-new-question-button').removeAttr('disabled');
                    $('#submit-new-question-button').text('Submit Question');

                }
                else{
                    $('#CL-form-add-question-status').text("Success!").delay(1000);
                    $('#CL_fullpage_transparentscreen').hide();
                    $('#CL-modal-add-question').hide();
                    $('#submit-new-question-button').removeAttr('disabled').text('Submit Question');
                }
                
            }, "json")
            /*.done(function(response){

            })
            .fail(function(response){
                console.log("failure on ajax_handle_post for new question creation");
                $('#CL-form-add-question-status').text("Error creating question: ");
                $('#submit-new-question-link').attr('disabled', 'false');
            })
            .always(function(response){

            })*/;
        
    }


    function onCancelCreateNewQuestion(){
        $('#CL_fullpage_transparentscreen').hide();
        $('#CL-modal-add-question').hide();

    }

</script>
