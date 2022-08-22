<?php
namespace App\Library\DomTemplate\Exception;

/**
 * Exception class UnknownReplacerName
 */
class DOMDocumentIsNULLException extends \Exception
{
    /**
     * @param \Throwable $prev The exception thrown from the caster
     */
    public function __construct(string $message = null, ?\Throwable $prev = null)
    {
        if (!empty($message)) {
            parent::__construct($message, 0, $prev);
        } elseif ($prev !== null) {
            parent::__construct('Empty or not settable \DOMDocument HTML document in class '.\get_class($prev).' thrown from a caster: '.$prev->getMessage(), 0, $prev);
        } else {
            parent::__construct('Empty or not settable \DOMDocument HTML document', 0, $prev);
        }
    }
}
