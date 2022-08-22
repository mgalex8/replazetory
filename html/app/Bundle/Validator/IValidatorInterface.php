<?php
namespace App\Bundle\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Mapping\ClassMetadata;

interface IValidatorInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function validate(Request $request);

    /**
     * @return Assert\Collection
     */
    public function rules(Request $request) : Collection;

    /**
     * @return mixed
     */
    public function messages(ClassMetadata $metadata);

    /**
     * @return mixed
     */
    public function getErrors() : array;
}