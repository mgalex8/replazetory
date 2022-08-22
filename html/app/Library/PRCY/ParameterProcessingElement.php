<?php

namespace App\Library\PRCY;

use DOMWrap\Element;

class ParameterProcessingElement
{

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param array $args
     * @param Element $element
     * @return array
     */
    public function processingParameter(array $args, ?Element $element, array $excludes = [])
    {
        $xpath = isset($excludes['xpath']) && $excludes['xpath'] ? null : $this->getXPath($args, $element);
        $has = isset($excludes['has']) && $excludes['has'] ? false : $this->getHas($args, $element);
        if ($has) {
            $item = [
                'xpath'         => $xpath,
                'id'            => isset($excludes['id']) && $excludes['id'] ? null : $this->getId($args, $element),
                'name'          => isset($excludes['name']) && $excludes['name'] ? null : $this->getName($args, $element),
                'value'         => isset($excludes['value']) && $excludes['value'] ? null : $this->getValue($args, $element),
                'html'          => isset($excludes['html']) && $excludes['html'] ? null : $this->getHtml($args, $element),
                'has'           => $has,
            ];
        } else {
            $item = [
                'xpath'         => $xpath,
                'id'            => isset($excludes['id']) && $excludes['id'] ? null : $this->getId($args, $element),
                'name'          => isset($excludes['name']) && $excludes['name'] ? null : $this->getName($args, $element),
                'value'         => '',
                'html'          => '',
                'has'           => $has,
            ];
        }
        return $item;
    }

    /**
     * @param string $key
     * @param \Closure $callback
     * @return mixed
     */
    public function cachable(string $key, \Closure $callback)
    {
        if (! isset($this->cache[ $key ])) {
            $this->cache[ $key ] = $callback();
        }

        return $this->cache[ $key ];
    }

    /**
     * @param array $args
     * @param Element $element
     * @return XPathFounder|null
     */
    public function getXPath(array $args, Element $element) : ?XPathFounder
    {
        return $this->cachable('xpath', function() use ($args, $element) {
            if (!empty($args['xpath'])) {
                if (is_callable($args['xpath']) && ! is_null($element)) {
                    $func = $args['xpath'];
                    $result = $func($element);
                    return $result instanceof XPathFounder ? $result : null;
                } elseif ($args['xpath'] instanceof XPathFounder) {
                    return $args['xpath']->setElement($element);
                } elseif (is_string($args['xpath'])) {
                    $xpath = str_replace('XPATH:', '', $args['xpath']);
                    return (new XPathFounder($xpath))->setElement($element);
                }
            }
            return null;
        });
    }

    /**
     * @param array $args
     * @param Element $element
     * @return string
     */
    public function getId(array $args, Element $element) : string
    {
        return $this->cachable('id', function() use ($args, $element) {
            if (isset($args['id']) && !empty($args['id'])) {
                if (is_callable($args['id']) && ! is_null($element)) {
                    $func = $args['id'];
                    return $func($element);
                } elseif ($args['id'] instanceof XPathFounder) {
                    return $args['id']->setElement($element)->id();
                } elseif (is_string($args['id']) && str_contains($args['id'], 'XPATH:')) {
                    return $this->getXPathFunctionResult($args, 'id', $element);
                } else {
                    return $args['id'];
                }
            }
            return uniqid('_');
        });
    }

    /**
     * @param array $args
     * @param Element $element
     * @return bool|mixed
     */
    public function getName(array $args, Element $element) : string
    {
        return $this->cachable('name', function() use ($args, $element) {
            if (isset($args['name']) && !empty($args['name'])) {
                if (is_callable($args['name'])) {
                    $func = $args['name'];
                    return $func($element);
                } elseif ($args['name'] instanceof XPathFounder) {
                    return $args['name']->setElement($element)->text();
                } elseif (is_string($args['name']) && str_contains($args['name'], 'XPATH:')) {
                    return $this->getXPathFunctionResult($args, 'name', $element);
                } else {
                    return $args['name'];
                }
            } elseif (!empty($args['xpath'])) {
                $xpathFounder = $this->getXPath($args, $element);
                return $xpathFounder ? $xpathFounder->setElement($element)->text() : '';
            }
            return $this->getId($args, $element);
        });
    }

    /**
     * @param array $args
     * @param Element $element
     * @return bool
     */
    public function getValue(array $args, Element $element) : string
    {
        return $this->cachable('value', function() use ($args, $element) {
            if (isset($args['value']) && !empty($args['value'])) {
                if (is_callable($args['value'])) {
                    $func = $args['value'];
                    return $func($element);
                } elseif ($args['value'] instanceof XPathFounder) {
                    return $args['value']->setElement($element)->text();
                } elseif (is_string($args['value']) && str_contains($args['value'], 'XPATH:')) {
                    return $this->getXPathFunctionResult($args, 'value', $element);
                } else {
                    return $args['value'];
                }
            } elseif (!empty($args['xpath'])) {
                $xpathFounder = $this->getXPath($args, $element);
                return $xpathFounder ? $xpathFounder->setElement($element)->text() : '';
            }
            return $element ? strip_tags($element->text()) : '';
        });
    }

    /**
     * @param array $args
     * @param Element $element
     * @return mixed|null
     */
    public function getHtml(array $args, Element $element) : string
    {
        return $this->cachable('html', function() use ($args, $element) {
            if (isset($args['html']) && !empty($args['html'])) {
                if (is_callable($args['html'])) {
                    $func = $args['html'];
                    return $func($element);
                } elseif ($args['html'] instanceof XPathFounder) {
                    return $args['html']->setElement($element)->html();
                } elseif (is_string($args['html']) && str_contains($args['html'], 'XPATH:')) {
                    return $this->getXPathFunctionResult($args, 'html', $element);
                } else {
                    return $args['html'];
                }
            } elseif (!empty($args['xpath'])) {
                $xpathFounder = $this->getXPath($args, $element);
                return $xpathFounder ? $xpathFounder->setElement($element)->html() : '';
            }
            return $element ? $element->html() : '';
        });
    }

    /**
     * @param array $args
     * @param Element $element
     * @return bool
     */
    public function getHas(array $args, Element $element) : bool
    {
        return $this->cachable('has', function() use ($args, $element) {
            if (isset($args['has'])) {
                if (is_callable($args['has'])) {
                    $func = $args['has'];
                    return (bool)$func($element);
                } elseif ($args['has'] instanceof XPathFounder) {
                    return (bool) $args['has']->setElement($element)->has();
                } elseif (is_string($args['has']) && str_contains($args['has'], 'XPATH:')) {
                    return (bool) $this->getXPathFunctionResult($args, 'has', $element);
                } else {
                    return (bool) $args['has'];
                }
            } elseif (!empty($args['xpath'])) {
                $xpathFounder = $this->getXPath($args, $element);
                return $xpathFounder ? (bool) $xpathFounder->setElement($element)->has() : false;
            } elseif (isset($args['name'])) {
                return strip_tags($element->html()) == $this->getName($args, $element);
            }
            return false;
        });
    }

    /**
     * Evalute attributes if exists
     * format for string attributes `$args` with key `$id` as `$args[ $id ]`
     * examples:
     *   XPATH://div[0] -> html
     *   XPATH://div[0] -> text()
     *   XPATH://div[0] -> has()
     *   XPATH://div[0] -> found ('Y'|'N')
     *   XPATH://div[0] -> found ("A"|"B"|"C")
     *   XPATH://div[0] -> found (X|S|M|L|XL|XXL)
     *
     * @param array $args
     * @param string $id
     * @param Element $element
     * @return null
     * @throws \Exception
     */
    protected function getXPathFunctionResult(array $args, string $id, Element $element)
    {
        $executable_xpath_string = str_replace('XPATH:', '', trim($args[ $id ]));
        $parts = explode('->', trim($executable_xpath_string));
        $xpath = trim($parts[0]);
        $xpathFounder = new XPathFounder($xpath);
//        $element = new Element($element->html());
        $xpathFounder->setElement($element);

        if (count($parts) > 1) {
            $result = null;

            for ($i = 1; $i < count($parts); $i++) {

                $explodes = explode("~", trim($parts[ $i ]));
                $end_scobes = "~";
                if (count($explodes) == 1) {
                    $explodes = explode("(", trim($parts[ $i ]));
                    $end_scobes = ")";
                }
                $method = trim($explodes[0]);

                /**
                 * find attributes if exists
                 * format for attributes examples:
                 *   XPATH://div[0] -> execute ('Y'|'N')
                 *   XPATH://div[0] -> execute ("A"|"B"|"C")
                 *   XPATH://div[0] -> execute (X|S|M|L|XL|XXL)
                 */
                $attributes = [];
                if (count($explodes) > 1) {
                    $attribute_string = rtrim(trim($explodes[1]), $end_scobes);
                    $attributes = explode('|', $attribute_string);
                    foreach ($attributes as $k => $attribute) {
                        $attributes[$k] = trim($attribute, "'\"\n\r\t\v\x00");
                    }
                }

                /**
                 * Call function for XPathFounder::$method() if exists
                 */
                if (!is_null($result) && $result instanceof XPathFounder) {
                    $result = $this->callXPathFounderFunction($result, $method, $attributes);
                } else {
                    $result = $this->callXPathFounderFunction($xpathFounder, $method, $attributes);
                }

                /**
                 * If evalute chain for function parts explodes '->' are break result object as not XPathFounder
                 * then throw error Exception and exit
                 */
                if (! $result instanceof XPathFounder && $i < count($parts) - 1) {
                    new \Exception(sprintf('Chain error[->]: Method %s::%s in chain %s are break result object as not XPathFounder', XPathFounder::class, $method, $executable_xpath_string));
                }
            }
            return $result;
        }
        elseif (count($parts) == 1) {
            return $this->callXPathFounderFunction($xpathFounder, $id, []);
        }
        else {
            return null;
        }
    }

    /**
     * @param XPathFounder $xpathFounder
     * @param string $method
     * @param array $attributes
     * @return mixed
     * @throws \Exception
     */
    protected function callXPathFounderFunction(XPathFounder $xpathFounder, string $method, $attributes)
    {
        if (method_exists($xpathFounder, $method)) {
            if (!empty($attributes)) {
                if (is_array($attributes)) {
                    return [$xpathFounder, $method] (...$attributes);
                } else {
                    return [$xpathFounder, $method] ($attributes);
                }
            } else {
                return [$xpathFounder, $method];
            }
        } else {
            throw new \BadMethodCallException(sprintf('Method not found: %s::%s', XPathFounder::class, $method));
        }
    }
}
