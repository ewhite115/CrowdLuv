<?php
// src/Acme/DemoBundle/EventListener/AcmeExceptionListener.php
namespace CrowdLuv\AdminBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;


class PageLoadListener
{
    

    public function onKernelRequest(GetResponseEvent $event){

		if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }
        
        //session_start();

        //if you are passing through any data
        $request = $event->getRequest();


		//Import/excute general CrowdLuv config / initialization code from 
		//	'Parent' CrowdLuv application
        require_once("../../inc/init_config.php"); 
   		//Set some session variables that can be output by the TWIG templates

        require_once("../../inc/cl_datafunctions.php");
		//Establish DB connection and global $CL_model object
		require_once("../../inc/init_db.php");
  		//Establish function and global var for debug/diagnostic
		require_once("../../inc/init_debug.php");
 		//Populate globals based on session info 
  		require_once("../../inc/init_sessionglobals.php");
  		
  		//Check for facebook session, create/update globals and DB accordingly
		//require_once("../../inc/init_facebook.php");

        //Get the Symfony session object from the request
        //$session = $request->getSession();                      
        //Get a session object that bridges to the 'legacy' PHP session variables 
        //set by the parent CrowdLuv app
		$session = new Session(new PhpBridgeSessionStorage());
		$session->start();
		$session->set('eddiesessvar', 'eddieeeee');
	    $session->set('CROWDLUV_ENV', CROWDLUV_ENV);   //Current Environment
        $session->set('BASE_URL', BASE_URL);
 	
		//'copy' key legacy-session info to Symfony session.
		//	this should happen after the above facebook initialization
		require_once("../../inc/init_sessionglobals_symfony.php");


		//$session->set('fb_user', $fb_user);
		//put facebook login URL into a session variable
		//  that the twig templates can use to create a login button
		if(isset($_SESSION['CL_fb_talentLoginURL'])) $session->set('fbloginurl', $_SESSION['CL_fb_talentLoginURL']);

		//echo "_session<pre>"; var_dump($_SESSION);echo"</pre>";
		//$session->set('fbAccessToken', $facebook->getAccessToken());
		if(isset($_SESSION['fb_token'])) $session->set('fbAccessToken', $_SESSION['fb_token']);



		// End CrowdLuv config/initializations

		
		//Test 'direct' db querying 
		//$tobj = $CL_model->get_talent_object_by_tid("1");
		//$session->set('talentname', $tobj['fb_page_name']);


    }



}

?>