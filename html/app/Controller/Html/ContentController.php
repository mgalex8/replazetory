<?php
namespace App\Controller\Html;

use App\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Bundle\YamlReplacerParser\ContentParser;
use App\Service\Database\TableNameGenerator;
use App\Service\XPathFilters\XPathFilterGenerator;
use App\Validator\Controller\Html\ContentValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Bundle\Plates\PlateView;

class ContentController extends AbstractController
{

    /**
     * Route('/content/insert_db?path=')
     * @param Request $request
     * @return void
     */
    public function insert_db(Request $request) : Response
    {
        $this->validate($request, new ContentValidator(), true);

        $command = new ContentParser();
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
        $this->validate($request, new ContentValidator(), true);

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
        $this->validate($request, new ContentValidator(), true);

        $path = $request->get('path');

        $filterGenerator = new XPathFilterGenerator();
        $filters = $filterGenerator->getFilters();

        $tableNameGenerator = new TableNameGenerator();
        $tables = $tableNameGenerator->getTables();

        $selected = [];

        return new Response(
            PlateView::render('content/replaced', [
                'content_url' => '/content/original?path='.$path,
                'filters' => $filters,
                'tables' => $tables,
                'selected' => $selected,
            ])
        );
    }
}