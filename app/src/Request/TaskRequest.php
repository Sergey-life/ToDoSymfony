<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

class TaskRequest
{
    public function isValid(array $params): ConstraintViolationListInterface|bool
    {
        if (empty($params)) {
            return false;
        }
        $validator = Validation::createValidator();
        $constraints = new Assert\Collection([
            'text' => [
                new Assert\NotBlank(),
                new Assert\Length([
                    'min' => 1,
                    'max' => 255
                ]),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9\s\-]+$/'
                ])
            ],
        ]);

        return $validator->validate($params, $constraints);
    }
}