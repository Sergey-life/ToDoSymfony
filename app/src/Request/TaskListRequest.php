<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

class TaskListRequest
{
    public function isValid(array $params): ConstraintViolationListInterface|bool
    {
        if (empty($params)) {
            return false;
        }

        $validator = Validation::createValidator();
        $constraints = new Assert\Collection([
            'page' => new Assert\Optional([
                new Assert\Type('digit'),
                new Assert\Positive(),
                new Assert\Range([
                    'min' => 1,
                    'max' => 50
                ])
            ]),
            'limit' => new Assert\Optional([
                new Assert\Type('digit'),
                new Assert\Positive(),
                new Assert\Range([
                    'min' => 1,
                    'max' => 50
                ])
            ]),
        ]);

        return $validator->validate($params, $constraints);
    }
}