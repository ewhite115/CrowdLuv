<?php

namespace CrowdLuv\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {


        return array('name' => "Ed");
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
