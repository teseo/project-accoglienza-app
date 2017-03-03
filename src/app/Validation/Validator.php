<?php
/**
 * Copyright (c) 2017. This file belongs to Misericordia di "Torre del lago Puccini"
 */

namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    /** @var  array */
    protected $errors;

    public function validate($request, $rules)
    {
        foreach ($rules as $field => $rule) {
            try {
                $rule->setName(ucfirst($field))->assert($request->getParam($field));
            } catch (NestedValidationException $e) {
                $this->errors[$field] = $e->getMessages();
            }
        }

        $_SESSION['errors'] = $this->errors;
        return $this;
    }

    public function failed()
    {
        return !empty($this->errors);
    }
}