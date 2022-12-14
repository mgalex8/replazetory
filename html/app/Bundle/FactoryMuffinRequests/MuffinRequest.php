<?php
namespace App\Bundle\FactoryMuffinRequests;

use League\FactoryMuffin\FactoryMuffin;
use Symfony\Component\HttpFoundation\Request;

class MuffinRequest extends Request
{

    /**
     * @var FactoryMuffin
     */
    protected $factoryMuffin;

    /**
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param $content
     */
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->factoryMuffin = new FactoryMuffin();
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param $content
     * @return void
     */
    public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        dump('initialize');
        dump($this->request);
        $this->make($request);
        $this->validate();
        parent::initialize($query, $request, $attributes, $cookies, $files, $server, $content); // TODO: Change the autogenerated stub
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param $content
     * @return MuffinRequest|void
     */
    public static function create(string $uri, string $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        dump('create');
        dump($parameters);
        parent::create($uri, $method, $parameters, $cookies, $files, $server, $content);
    }

    protected function make($parameters)
    {
        dump('make');
        dump($parameters);
    }

    public function validate()
    {
        dump('validate');
    }


}