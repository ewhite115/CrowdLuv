<?php
// src/Acme/DemoBundle/EventListener/AcmeExceptionListener.php
namespace CrowdLuv\AdminBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;


class PageLoadListener
{
    

    public function onKernelRequest(GetResponseEvent $event){

        //if you are passing through any data
        $request = $event->getRequest();

        //if you need to update the session data
        $session = $request->getSession();              

        $session->set('eddiesessvar', 'eddieeeee');


        //Whatever else you need to do...
        




    }



}

