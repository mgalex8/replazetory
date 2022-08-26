<?php
namespace App\Validator\Controller\Html;

use App\Bundle\Validator\AbstractValidator;
use App\Bundle\Validator\IValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;

class ContentValidator extends AbstractValidator implements IValidatorInterface
{

    /**
     * @param Request $request
     * @return Assert\Collection
     */
    public function rules(Request $request) : Assert\Collection
    {
        return new Assert\Collection([
            'path' => [
                new Assert\Required(),
                new Assert\NotBlank(),
            ],
        ]);
    }

}