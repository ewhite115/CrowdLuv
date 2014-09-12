<?php

namespace CrowdLuv\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }


 /**
     * @Route("/edtest/{name}")
     * @Template()
     */
    public function edtestAction($name)
    {
        return array('name' => $name);
    }

 /**
     * @Route("/followerlist/")
     * @Template()
     */
    public function followerlistAction()
    {
        return array('name' => "flist");
    }



}
