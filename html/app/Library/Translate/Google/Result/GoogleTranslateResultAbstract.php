<?php
namespace App\Library\Translate\Google\Result;

/**
 * GoogleTranslate class
 */
abstract class GoogleTranslateResultAbstract
{

    /**
     * @var string|null
     */
    public ?string $status;

    /**
     * @var int|null
     */
    public ?int $response_code;

    /**
     * @var string|null
     */
    public ?string $source_lang;

    /**
     * @var string|null
     */
    public ?string $target_lang;

    /**
     * @var string|null
     */
    public ?string $text;

    /**
     * Class GoogleTranslateResultAbstract Constructor.
     * @param object|array$data
     * @throws \Exception
     */
    public function __construct($data)
    {
        if (is_array($data) || ! is_object($data)) {
            throw new \Exception(sprintf('Argument 0 in method %s::%s must be type of array or object StdClass, but given %s', __CLASS__, __METHOD__, gettype($data)));
        }

        $data = (array) $data;
        $this->status = $data['status'] ?? null;
        $this->response_code = $data['response_code'] ?? null;
        $this->source_lang = $data['source_lang'] ?? null;
        $this->target_lang = $data['target_lang'] ?? null;
        $this->text = $data['text'] ?? null;
    }

    /**
     * @param string $source_lang
     * @return GoogleTranslateResultAbstract
     */
    public function setStatus(string $status): GoogleTranslateResultAbstract
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->response_code;
    }

    /**
     * @param string $source_lang
     * @return GoogleTranslateResultAbstract
     */
    public function setResponseCode(int $response_code): GoogleTranslateResultAbstract
    {
        $this->response_code = $response_code;
        return $this;
    }

    /**
     * @param string $source_lang
     * @return GoogleTranslateResultAbstract
     */
    public function setSourceLang(string $source_lang): GoogleTranslateResultAbstract
    {
        $this->source_lang = $source_lang;
        return $this;
    }

    /**
     * @return string
     */
    public function getSourceLang(): ?string
    {
        return $this->source_lang;
    }

    /**
     * @param string $target_lang
     * @return GoogleTranslateResultAbstract
     */
    public function setTargetLang(string $target_lang): GoogleTranslateResultAbstract
    {
        $this->target_lang = $target_lang;
        return $this;
    }

    /**
     * @return string
     */
    public function getTargetLang(): ?string
    {
        return $this->target_lang;
    }

    /**
     * @param string $text
     * @return GoogleTranslateResultAbstract
     */
    public function setText(string $text): GoogleTranslateResultAbstract
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

}
