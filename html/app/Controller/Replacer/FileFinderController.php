<?php
namespace App\Controller\Replacer;

use Knp\Component\Pager\Paginator;
use App\Bundle\Plates\Plate;
use App\Bundle\Plates\PlateView;
use App\Serializer\Pagination\PaginationResultSerializer;
use App\Service\FileFinder\FileFinder;
use App\Validator\Controller\Replacer\FileFinderFilesValidator;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileFinderController extends AbstractController
{

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->finder = new Finder();
    }

    /**
     * Route('/replacer/files')
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        ini_set('memory_limit', '2048M');
        $directories = [];
        $path = ABS_PATH . '/_HTML';

        $fileFinder = new FileFinder();
        $find_directories = $fileFinder->findDirectories($path);


        foreach ($find_directories as $directory) {
//            $files = $fileFinder->findFiles($directory, '/\.html/');
//            if (count($files) > 0) {
                $directories[] = $directory;
//            }
        }

        return new Response(
            PlateView::render('filefinder/index', ['directories' => $directories])
        );
    }

    /**
     * Route('/replacer/files')
     * @param Request $request
     * @return Response
     */
    public function files(Request $request)
    {
        $this->validate($request, new FileFinderFilesValidator(), true);

        ini_set('memory_limit', '2048M');

        $path = ABS_PATH . \DIRECTORY_SEPARATOR . ltrim($request->get('dir'), ABS_PATH);
        $sort = $request->get('sort') ?: null;
        $page = $request->get('page') ?: 1;
        $limit = $request->get('limit') ?: 200;

        $fileFinder = new FileFinder();
        $files = $fileFinder->findFiles($path, '/\.html/', $sort);

        $filesList = [];
        foreach ($files as $file) {
            $filesList[] = (string) $file;
        }

        $paginator = new Paginator();
        $pagination = $paginator->paginate($filesList, $page, 200);
        $paginationSerializer = new PaginationResultSerializer($pagination);

        return new JsonResponse(array('path' => $path, 'files' => $paginationSerializer->toArray()));
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function filechecker()
    {
        $filesystem = new Filesystem();

        $path = Path::normalize(sys_get_temp_dir().'/'.random_int(0, 1000));
//        $filesystem->mkdir($path, 0755, true);

        $filesystem->isAbsolutePath('../dir');

        // returns 'videos/'
        $filesystem->makePathRelative('/tmp/videos', '/tmp');

        // returns a path like : /tmp/prefix_wyjgtF.png
        $filesystem->tempnam('/tmp', 'prefix_', '.png');

//        $filesystem->appendToFile('logs.txt', 'Email sent to user@example.com');

        // return /var/www/vhost/config.ini
        echo Path::canonicalize('/var/www/vhost/webmozart/../config.ini');

        // => /var/www/project/config/config.yaml
        echo Path::makeAbsolute('config/config.yaml', '/var/www/project');

        // => /usr/share/lib/config.ini
        echo Path::makeAbsolute('/usr/share/lib/config.ini', '/var/www/project');

        // => /var/www/project/config/config.yaml
        echo Path::makeAbsolute('../config/config.yaml', '/var/www/project/uploads');

        // => config/config.yaml
        echo Path::makeRelative('/var/www/project/config/config.yaml', '/var/www/project');

        // => ../config/config.yaml
        echo Path::makeRelative('/var/www/project/config/config.yaml', '/var/www/project/uploads');

        // => true
        Path::isAbsolute('/etc');

        // => /var/www/vhosts/project/httpdocs
        Path::getLongestCommonBasePath(
            '/var/www/vhosts/project/httpdocs/config/config.yaml',
            '/var/www/vhosts/project/httpdocs/config/routing.yaml',
            '/var/www/vhosts/project/httpdocs/config/services.yaml',
            '/var/www/vhosts/project/httpdocs/images/banana.gif',
            '/var/www/vhosts/project/httpdocs/uploads/images/nicer-banana.gif'
        );






    }

}