<?php

namespace Biin2013\Tiger\Foundation;

use Illuminate\Contracts\Validation\Validator;

abstract class Validation
{
    protected bool $appendDefaultRules = true;
    /**
     * @var array<string>|null
     */
    protected ?array $onlyRules = null;
    /**
     * @var array<string>|null
     */
    protected ?array $exceptRules = null;

    /**
     * @return array<string, array>
     */
    abstract protected function rules(): array;

    /**
     * @return array<int>
     */
    abstract protected function codes(): array;

    /**
     * @return string[][]
     */
    private function defaultRules(): array
    {
        return [
            'seq' => ['bail', 'integer', 'between:0,255'],
            'brief' => ['max:255']
        ];
    }

    /**
     * @return int[]
     */
    private function defaultCodes(): array
    {
        return [
            'seq.integer' => 1101,
            'seq.between' => 1102,
            'brief.max' => 1103
        ];
    }

    /**
     * @return array<string, array>
     */
    protected function allRules(): array
    {
        return $this->appendDefaultRules
            ? array_merge($this->defaultRules(), $this->rules())
            : $this->rules();
    }

    /**
     * @return array<string, array>
     */
    public function getRules(): array
    {
        $rules = $this->allRules();

        if ($this->onlyRules) {
            return array_intersect_key($rules, array_flip($this->onlyRules));
        }

        if ($this->exceptRules) {
            return array_diff_key($rules, array_flip($this->exceptRules));
        }

        return $rules;
    }

    /**
     * @param string $field
     * @param string $rule
     * @return int
     */
    public function resolveCode(string $field, string $rule): int
    {
        return $this->allCodes()[$field . '.' . $rule] ?? 10000;
    }

    /**
     * @return array<int>
     */
    private function allCodes(): array
    {
        return $this->appendDefaultRules
            ? array_merge($this->defaultCodes(), $this->codes())
            : $this->codes();
    }

    /**
     * @param array<string, mixed> $data
     * @param array|null $routeParams
     * @param Validator $validator
     */
    public function validated(array $data, ?array $routeParams, Validator $validator): void
    {

    }
}