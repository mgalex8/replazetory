<?php
namespace App\Library\DomTemplate\Replacer\Traits;

/**
 * ReplacerClosures trait
 */
trait ReplacerClosures
{

    /**
     * @var array
     */
    protected $closures = [];

    /**
     * @param \Closure $closure
     * @param string $template
     * @return void
     */
    public function setFunctionClosureGetCurrentTemplate(\Closure $closure) {
        return $this->setFunctionClosure('getCurrentTemplate', $closure);
    }

    /**
     * @param \Closure $closure
     * @param string $template
     * @return void
     */
    public function setFunctionClosureIsTemplate(\Closure $closure) {
        return $this->setFunctionClosure('isTemplate', $closure);
    }

    /**
     * @param \Closure $closure
     * @param string $template
     * @return void
     */
    public function setFunctionClosureGetIndexFromTemplate(\Closure $closure) {
        return $this->setFunctionClosure('getIndexFromTemplate', $closure);
    }

    /**
     * @param string $method
     * @param \Closure $closure
     * @param array $args
     * @return void
     */
    public function setFunctionClosure(string $method, \Closure $closure) : void {
        if (! method_exists($this, $method)) {
            throw new \BadMethodCallException(sprintf('Not found method %s::%s() for class %s', __CLASS__, $method, __CLASS__));
        }
        $this->closures[ $method ] = $closure;
    }

    /**
     * @param string $method
     * @return bool
     */
    public function existsCLosure(string $method) : bool {
        return isset($this->closures[ $method ]);
    }

    /**
     * @param string $method
     * @param ...$args
     * @return mixed
     */
    protected function __callClosure(string $method, ...$args) {
        if (!empty($args)) {
            if (is_array($args)) {
                return call_user_func( $this->closures[ $method ], ...$args );
            } else {
                return call_user_func( $this->closures[ $method ], $args );
            }
        } else {
            return call_user_func( $this->closures[ $method ] );
        }
    }

}
