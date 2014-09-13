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
        
        //if you are passing through any data
        $request = $event->getRequest();
        //Get the Symfony session object from the request
        //$session = $request->getSession();              
        
        //Get a session object that bridges to the 'legacy' PHP session variables 
        //set by the parent CrowdLuv app
		$session = new Session(new PhpBridgeSessionStorage());
		$session->set('eddiesessvar', 'eddieeeee');

		//Import/excute general CrowdLuv config / initialization code from 
		//	'Parent' CrowdLuv application
        require_once("../../inc/init_config.php"); 
   		//Set some session variables that can be output by the TWIG templates
	    $session->set('CROWDLUV_ENV', CROWDLUV_ENV);   //Current Environment
        $session->set('BASE_URL', BASE_URL);
 
        require_once("../../inc/cl_datafunctions.php");
		//Establish DB connection and global $CL_model object
		require_once("../../inc/init_db.php");
  		//Establish function and global var for debug/diagnostic
		require_once("../../inc/init_debug.php");
 		//Populate globals based on session info 
  		require_once("../../inc/init_sessionglobals.php");
  		
  		//Check for facebook session, create/update globals and DB accordingly
		require_once("../../inc/init_facebook.php");
		//'copy' key legacy-session info to Symfony session
		require_once("../../inc/init_sessionglobals_symfony.php");
		$session->set('fb_user', $fb_user);
		//Generate facebook login URL and set it into a session variable
		//  that the twig templates can use to create a login button
		$talparams = array('scope' => CL_FB_TALENT_PERMISSION_SCOPE_STRING);
		$fbloginurl = $facebook->getLoginUrl($talparams);
		$session->set('fbloginurl', $fbloginurl);

		// End CrowdLuv config/initializations


		
		//Test 'direct' db querying 
		//$tobj = $CL_model->get_talent_object_by_tid("1");
		//$session->set('talentname', $tobj['fb_page_name']);


    }



}

?>