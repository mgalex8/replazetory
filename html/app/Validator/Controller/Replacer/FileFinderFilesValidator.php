<?php
namespace App\Validator\Controller\Replacer;

use App\Bundle\Validator\AbstractValidator;
use App\Bundle\Validator\IValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;

class FileFinderFilesValidator extends AbstractValidator implements IValidatorInterface
{

    /**
     * @param Request $request
     * @return Assert\Collection
     */
    public function rules(Request $request) : Assert\Collection
    {
        $sortChoices = ['filename', 'type', 'accessed_at', 'changed_at', 'modified_at'];
        return new Assert\Collection([
            'dir' => [
                new Assert\Required(),
                new Assert\NotBlank(),
            ],
            'sort' => [
                new Assert\Optional(
                    new Assert\Choice([
                        'choices' => $sortChoices,
                        'message' => sprintf('Не верное значение в поле {%s}. Укажите одно из значений %s', $this->getAttribute('sort'), implode(', ', $sortChoices))
                    ])
                ),
            ],
        ]);
    }

    /**
     * @return array
     */
    public function attributes() : array
    {
        return [
            'sort' => 'Сортировка',
        ];
    }

    public function messages(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('dir', new Assert\Required([
            'message' => 'Поле {dir} обязательно для заполнения',
        ]));
    }

}