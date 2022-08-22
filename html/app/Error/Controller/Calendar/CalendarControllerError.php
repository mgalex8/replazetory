<?php
namespace App\Error\Controller\Calendar;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CalendarControllerException
 */
class CalendarControllerError
{
    /**
     * @param FlattenException $exception
     * @return Response
     */
    public function exception(FlattenException $exception)
    {
        $msg = 'Something went wrong! ('.$exception->getMessage().')';

        return new Response($msg, $exception->getStatusCode());
    }
}