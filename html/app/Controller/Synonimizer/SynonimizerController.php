<?php
namespace App\Controller\Synonimizer;

use App\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Library\Synonimizer\Filters\GetTextContentFilter;
use App\Library\Synonimizer\Filters\IContentFilter;
use App\Library\Synonimizer\Synonimizer;
use App\Validator\Controller\Synonimizer\SynonimizerValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Bundle\Plates\PlateView;

/**
 * Class SynonimizerController
 */
class SynonimizerController extends AbstractController
{

    /**
     * @var array
     */
    protected $filters;

    /**
     * Route('/synonimizer/synonimizer?text=')
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function synonimizer(Request $request) : Response
    {
        $this->validate($request, new SynonimizerValidator(), true);

        $text = $request->get('text');
        if ($text) {
            $synonimizer = new Synonimizer();
            $synonimizer->setFilter(new GetTextContentFilter());
            $synonims = $synonimizer->synonimize($text);
        } else {
            $synonims = '';
        }

        return new Response(
            PlateView::render('synonimizer/synonimizer', [
                'text' => $text,
                'synonims' => $synonims,
            ])
        );
    }

}