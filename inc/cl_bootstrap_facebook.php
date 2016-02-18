<?php

  use Facebook\FacebookRequest;
 
 
  /** Check for Facebook Permissions Denied & Redirect
   * TODO:  is this still relvant in api 4.0?
   * 
   * If this was the first time the user tried to login, but they denied
   * the facebook permission dialog, the query string will include the following
   * provided by facebook
   *     ?error=access_denied&error_code=200&error_description=Permissions+error&error_reason=user_denied&state=939d1a76d41e3612ff16087f39afc14c#_=_
   * So, check for this, and if found, redirect to the home page with
   * a flad to include explanation that the permission are required
   */
  if((isset( $_GET['error_reason'] ) && $_GET['error_reason'] == 'user_denied')){
     header('Location: ' . CLADDR . "?fb_user_denied_permissions=1" );
     die(); 
  }

  /** Check for facebook session  */

  if ($clFacebookHelper->getFacebookSession()) { 

    //If a session was found,  inject it as a dependency on the CL model
    $CL_model->setFacebookSession($clFacebookHelper->getFacebookSession()); 
    
    //Call getLoggedinUserObj - removed 2/17  dont need to do this until it is needed
    //$clRequestInformation->getLoggedInUserObj();

    /**  Managed-Pages Import from facebook
     * Now check for facebook pages the user is an administrator of,
     * add them to CL db if new. 
     */
    $clRequestInformation->getManagedBrands();


 }





  /**  Facebook Likes
   *  Check for facebook pages the user 'likes',
   *   add those pages to CL db (as new brands) if not already present
   *   add an entry in db indicating this user "likes" that page/talent 
   */
  if($clFacebookHelper->getFacebookSession() and $clFacebookHelper->isNewSession){
    //We may need to make multiple requests to get all the likes.
    //  Loop making api call ..  
    $done=false;
    //Create the initial request object for retrieving user's likes
    $request = new FacebookRequest( $clFacebookHelper->getFacebookSession(), 'GET', '/me/likes?fields=id,name,category,link&limit=100' );
    do{  
      try{          
          $response = $request->execute();
          // get response
          $fb_user_likes = $response->getGraphObject()->asArray();
          //echo "<pre>"; var_dump($fb_user_likes); echo "</pre>"; die;
          
          if(isset($fb_user_likes['data']) && sizeof($fb_user_likes['data']) > 0) {  
              
              foreach ($fb_user_likes['data'] as $fbupg) {
                  //...See if it already exists as a talent in the CL DB
                  $cltid = $CL_model->get_crowdluv_tid_by_fb_pid($fbupg->id);
                  //If not, and it's in an "enabled" category, add it
                  if(! $cltid && (in_array($fbupg->category, CrowdLuvFacebookHelper::$facebookLikeCategoriesToCreateStubsFor))) {
                      cldbgmsg("Found new facebook like page to add: " . $fbupg->id . ":" . $fbupg->name . ":" . $fbupg->category); 
                      $CL_model->create_new_cl_talent_record_from_facebook_user_like($fbupg);
                      $cltid = $CL_model->get_crowdluv_tid_by_fb_pid($fbupg->id);
                      
                  }
                  //Make sure DB is updated to reflect that this user facebook-likes the talent
                  if($cltid) $CL_model->setFollower_FacebookLikes_Talent($clRequestInformation->getLoggedInUserId(), $cltid, 1); 


              }//foreach
          } //if we got data back fro api call

      }catch (FacebookApiException $e) {
        cldbgmsg("FacebookAPIException requesting /me/likes -------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
        $fb_user_likes = null;
        //we should still be able to proceed, since the rest of the pages do not rely on 
        //  fb_user_likes, and should continue to use the talent array in the session var
      } 
      //Create a new request object and start over if there are more likes
    } while (($response) && $request = $response->getRequestForNextPage());
  }//  Facebook likes import


  //Clear the 'response' object created above so that it doesnt cause problems for 
  //  ajax files which use the same variable name
  $response = array();


