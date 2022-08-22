<?php
namespace App\Controller\Messages;

use App\Service\MessageGenerator;
use Creitive\Breadcrumbs\Breadcrumbs;
use App\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MessageController
 */
class MessageController extends AbstractController
{
    /**
     * [Route('/messages/new/{id}')]
     */
    public function new(Request $request): Response
    {
        $breadcrumbs = new Breadcrumbs();

        $breadcrumbs->addCrumb('Home', '/');

        echo $breadcrumbs->render();

        $breadcrumbs->addCrumb('Home', '/');
        $breadcrumbs->add('Home', '/');
        $breadcrumbs->addCrumb('Home', '/');
        $breadcrumbs->addCrumb('Pages', 'pages');
        $breadcrumbs->addCrumb('Subpage', 'subpage');
        $breadcrumbs->addCrumb('Subsubpage', '/subsubpage');
        $breadcrumbs->addCrumb('Other website', 'http://otherwebsite.com/some-page');
        echo $breadcrumbs->render();

        $breadcrumbs
            ->addCrumb('Home', '/')
            ->addCrumb('Pages', 'pages')
            ->addCrumb('Subpage', 'subpage')
            ->addCrumb('Subsubpage', '/subsubpage')
            ->addCrumb('Other website', 'http://otherwebsite.com/some-page');

        $classes = array('class1', 'class2');

        $breadcrumbs->setCssClasses($classes);
        $breadcrumbs->addCssClasses($classes);
        $breadcrumbs->removeCssClasses($classes);

        $stringClasses = 'class1 class2 class3';
        $arrayClasses = array('class4', 'class5');

        $breadcrumbs->addCssClasses($stringClasses);
        $breadcrumbs->addCssClasses($arrayClasses);

        $breadcrumbs->setDivider('»');
        $breadcrumbs->setListElement('ol');
        $breadcrumbs->setLastItemWithHref(true);
        echo $breadcrumbs->render();

        $messageGenerator = new MessageGenerator();
        $message = $messageGenerator->getHappyMessage();

        return new Response(
            sprintf("<div> Hello %s <br> %s </div>", $request->get('id'), $message)
        );
    }

    /**
     * [Route('/messages/dp/{id}')]
     */
    public function dep(Request $request, MessageGenerator $messageGenerator): Response
    {
        $message = $messageGenerator->getHappyMessage();

        return new Response(
            sprintf("<div> Hello %s <br> %s </div>", $request->get('id'), $message)
        );
    }
}