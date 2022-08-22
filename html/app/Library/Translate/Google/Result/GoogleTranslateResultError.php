<?php
namespace App\Library\Translate\Google\Result;

/**
 * GoogleTranslate class
 */
class GoogleTranslateResultError extends GoogleTranslateResultAbstract
{

    /**
     * @var string|null
     */
    public ?array $errors;

    /**
     * Class GoogleTranslateResultError Constructor.
     * @param object|array $data
     * @throws \Exception
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $data = (array) $data;
        $this->errors = $data['errors'] ?? null;
    }

    /**
     * @param array $errors
     * @return GoogleTranslateResultError
     */
    public function setErrors(array $errors): GoogleTranslateResultError
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

}
