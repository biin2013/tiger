<?php

namespace Biin2013\Tiger\Foundation\Concerns;

use Biin2013\Tiger\Exceptions\ValidationException;
use Exception;
use Illuminate\Support\Facades\Validator as BaseValidator;

trait HasValidate
{
    use ResolveHandler;

    /**
     * @param array<string, mixed> $data
     * @param array<mixed>|null $routeParams
     * @throws ValidationException
     * @throws Exception
     */
    protected function validate(array $data, ?array $routeParams = null): void
    {
        $validation = $this->getValidateHandler();
        $messages = $validation->getMessages();
        $validator = BaseValidator::make(
            $data,
            $validation->getRules(),
            $messages,
            $validation->getAttributes()
        );

        if ($validator->fails()) {
            $failed = $validator->failed();
            $field = array_key_first($failed);
            $key = lcfirst(array_key_first($failed[$field]));

            throw new ValidationException(
                $validation->resolveCode($field, $key),
                $messages[$field][$key] ?? $validator->errors()->first(),
                $validation->resolveData($field, $key)
            );
        }

        $validation->validated($data, $routeParams, $validator);
    }
}