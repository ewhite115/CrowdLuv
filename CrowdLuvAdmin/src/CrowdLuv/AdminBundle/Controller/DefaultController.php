<?php

namespace CrowdLuv\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;


class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {

        $session = new Session(); 
        return array('name' => $session->get('eddiesessvar'));
    }



 /**
     * @Route("/edtest/{name}")
     * @Template()
     */
    public function edtestAction($name)
    {
        return array('name' => $name);
    }




}
