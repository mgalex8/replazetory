<?php
namespace App\Controller\Home;

use Psr\Log\LoggerInterface;
use App\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController  extends AbstractController
{

    /**
     * @Route("/home")
     */
    public function index(Request $request) : Response
    {
        //$logger->info('Look, I just used a service!');
        return new Response(
            sprintf("Index Home")
        );

//        return new Response(
//            sprintf("Hello %s", $request->get('name'))
//        );
    }

    /**
     * @Route("/home/{text}")
     */
    public function gettext(Request $request, $text) : Response
    {
        return new Response(
            sprintf("Hello %s", $request->get('text'))
        );
    }

//    /**
//     * @Route("/home")
//     */
//    public function index(Request $request, LoggerInterface $logger): Response
//    {
//        $logger->info('Look, I just used a service!');
//        return new Response(
//            sprintf("Hello %s", 'test')
//        );
//
////        return new Response(
////            sprintf("Hello %s", $request->get('name'))
////        );
//    }
}