<?php
namespace App\Bundle\Validator;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validation;

abstract class AbstractValidator implements IValidatorInterface
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * @var array
     */
    protected $errors;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->validator = Validation::createValidator();
        $this->errors = [];
    }

    /**
     * @param Request $request
     * @return mixed|void
     */
    public function validate(Request $request)
    {
        $violations = $this->validator->validate($request->query->all(), $this->rules($request));
        foreach ($violations as $violation) {
            $this->setErrorMessage(
                $violation->getPropertyPath(),
                $violation->getMessage()
            );
        }
        return count($this->errors) == 0;
    }

    /**
     * @param string $path
     * @param string $message
     * @return void
     */
    public function setErrorMessage(string $path, string $message)
    {
        $this->errors[] = new ParameterBag([
            'path' => $path,
            'message' => $message,
        ]);
    }

    /**
     * @return mixed
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function getAttribute(string $name)
    {
        $attributes = $this->attributes();
        if (! isset($attributes[$name])) {
            throw new \Exception(sprintf('Attribute %s not found', $name));
        }
        return $attributes[$name];
    }

    /**
     * @return array
     */
    public function attributes() : array
    {
        return [];
    }

    /**
     * @param ClassMetadata $metadata
     * @return void
     */
    public function messages(ClassMetadata $metadata) : void {}

    /**
     * @return Assert\Collection
     */
    abstract public function rules(Request $request) : Collection;
}