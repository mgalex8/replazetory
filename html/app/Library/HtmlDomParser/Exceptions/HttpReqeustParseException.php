<?php
namespace App\Library\HtmlDomParser\Exceptions;

/**
 * Exception class HttpReqeusetParseException
 */
class HttpReqeustParseException extends \Exception
{

    /**
     * @var array
     */
    protected $response;

    /**
     * @param $message
     * @param $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param $response
     * @return void
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

}
