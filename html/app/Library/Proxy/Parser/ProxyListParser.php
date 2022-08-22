<?php
namespace App\Library\Proxy\Parser;

use App\Library\Proxy\Parser\Interfaces\IProxyParserEngines;

class ProxyListParser
{

    /**
     * @var IProxyParserEngines
     */
    protected IProxyParserEngines $engine;

    public function __construct(IProxyParserEngines $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return void
     */
    public function parse($depth = 1)
    {
        return $this->engine->parse($depth = 1);
    }


}
