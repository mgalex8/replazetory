<?php
namespace App\Library\DomTemplate\Exception;

/**
 * Exception class UnknownReplacerName
 */
class UnregisteredReplacerNameException extends \Exception
{
    /**
     * @param \Throwable $prev The exception thrown from the caster
     */
    public function __construct(string $message = null, string $replacer_name = null, ?\Throwable $prev = null)
    {
        if (!empty($message)) {
            parent::__construct($message, 0, $prev);
        } elseif (!empty($replacer_name) && $prev !== null) {
            parent::__construct('Call unregistered replacer name \''.htmlspecialchars($replacer_name).'\' in class '.\get_class($prev).' thrown from a caster: '.$prev->getMessage(), 0, $prev);
        } elseif (!empty($replacer_name)) {
            parent::__construct('Call unregistered replacer name \'' . htmlspecialchars($replacer_name).'\'', 0, $prev);
        } elseif (!empty($prev)) {
            parent::__construct('Call unregistered replacer in class '.\get_class($prev).' thrown from a caster: '.$prev->getMessage(), 0, $prev);
        } else {
            parent::__construct('Call unregistered replacer', 0, $prev);
        }
    }
}
