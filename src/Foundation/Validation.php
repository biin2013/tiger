<?php

namespace Biin2013\Tiger\Foundation;

use Illuminate\Contracts\Validation\Validator;

abstract class Validation
{
    /**
     * @var bool
     */
    protected bool $appendDefaultRules = false;
    /**
     * @var array<string>|null
     */
    protected ?array $onlyRules = null;
    /**
     * @var array<string>|null
     */
    protected ?array $exceptRules = null;

    /**
     * @var array<string, mixed>
     */
    protected array $data = [];

    public function __construct()
    {
        $this->data = $this->allData();
    }

    /**
     * @return array<string, array<mixed>>
     */
    abstract protected function rules(): array;

    /**
     * @return array<string, array<mixed>>
     */
    abstract protected function codes(): array;

    /**
     * @return string[][]
     */
    protected function defaultRules(): array
    {
        return [
            'seq' => ['bail', 'integer', 'between:0,255'],
            'brief' => ['max:255']
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultCodes(): array
    {
        return [
            'seq' => [
                'integer' => 1101,
                'between' => 1102
            ],
            'brief' => [
                'max' => 1103
            ]
        ];
    }

    /**
     * @return array<string, array<mixed>>
     */
    protected function allRules(): array
    {
        return $this->appendDefaultRules
            ? array_merge($this->defaultRules(), $this->rules())
            : $this->rules();
    }

    /**
     * @return array<string, array<mixed>>
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
        return $this->allCodes()[$field][$rule] ?? 10000;
    }

    /**
     * @return array<string, mixed>
     */
    private function allCodes(): array
    {
        return $this->appendDefaultRules
            ? array_merge_recursive($this->defaultCodes(), $this->codes())
            : $this->codes();
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultData(): array
    {
        return [
            'seq' => ['between' => ['min' => 0, 'max' => 255]],
            'brief' => ['max' => 255]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function data(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    private function allData(): array
    {
        return array_merge_recursive($this->defaultData(), $this->data());
    }

    /**
     * @param string $field
     * @param string $rule
     * @return array<string, mixed>
     */
    public function resolveData(string $field, string $rule): array
    {
        $data = $this->data[$field][$rule] ?? null;

        return $data ? [$rule => $data] : [];
    }

    /**
     * @return mixed[]
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * @return mixed[]
     */
    public function getMessages(): array
    {
        return [];
    }

    /**
     * @return mixed[]
     */
    protected function attributes(): array
    {
        return [];
    }

    /**
     * @return mixed[]
     */
    public function getAttributes(): array
    {
        return [];
    }

    /**
     * @param array<string, mixed> $data
     * @param array<mixed>|null $routeParams
     * @param Validator $validator
     */
    public function validated(array $data, ?array $routeParams, Validator $validator): void
    {

    }
}