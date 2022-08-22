<?php
namespace App\Controller\Html;

use App\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Command\ParseIntoDbCommand;

class ContentController extends AbstractController
{

    /**
     * Route('/content/insert_db?path=')
     * @param Request $request
     * @return void
     */
    public function insert_db(Request $request) : Response
    {
        $command = new ParseIntoDbCommand();
        $command->parse($request->get('path'));

        return new Response(
            ''
        );
    }

    /**
     * Route('/content/original?path=')
     * @param Request $request
     * @return Response
     */
    public function original(Request $request) : Response
    {
        $path = $request->get('path');
        $content = file_get_contents($path);

        return new Response(
            $content
        );
    }

    /**
     * Route('/content/replaced?path=')
     * @param Request $request
     * @return Response
     */
    public function replaced(Request $request) : Response
    {
        $path = $request->get('path');
        $content = file_get_contents($path);

        return new Response(
            $content
        );
    }
}