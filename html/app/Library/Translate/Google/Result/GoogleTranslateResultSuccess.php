<?php
namespace App\Library\Translate\Google\Result;

/**
 * GoogleTranslate class
 */
class GoogleTranslateResultSuccess extends GoogleTranslateResultAbstract
{

    /**
     * @var string|null
     */
    public ?string $result_text;

    /**
     * Class GoogleTranslateResultSuccess Constructor.
     * @param object|array $data
     * @throws \Exception
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $data = (array) $data;
        $this->result_text = $data['result_text'] ?? null;

    }

    /**
     * @param string $result_text
     * @return GoogleTranslateResultSuccess
     */
    public function setResultText(string $result_text): GoogleTranslateResultSuccess
    {
        $this->result_text = $result_text;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getResultText(): ?string
    {
        return $this->result_text;
    }

}
