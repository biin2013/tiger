<?php

namespace Biin2013\Tiger\Foundation;

use Biin2013\Tiger\Exceptions\ValidationException;
use Biin2013\Tiger\Foundation\Concerns\HasValidate;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

abstract class Logic
{
    use HasValidate;

    protected bool $dbTransaction = true;

    /**
     * @param array<mixed>|null $routeParams
     * @param array<string, mixed> $data
     * @return array<mixed>|JsonResponse|Arrayable<int|string, mixed>
     */
    abstract protected function run(?array $routeParams, array $data): array|JsonResponse|Arrayable;

    /**
     * @param array<mixed>|null $routeParams
     * @param array<string, mixed> $data
     * @param bool $validate
     * @param bool|null $dbTransaction
     * @return array<int|string, mixed>|JsonResponse|Arrayable<int|string, mixed>
     * @throws ValidationException
     */
    public function __invoke(
        ?array $routeParams,
        array  $data,
        bool   $validate = false,
        ?bool  $dbTransaction = null
    ): array|JsonResponse|Arrayable
    {
        if ($validate) {
            $this->setCurrentHandlerType()->validate($data, $routeParams);
        }

        return ($dbTransaction ?? $this->dbTransaction)
            ? DB::transaction(fn() => $this->run($routeParams, $data))
            : $this->run($routeParams, $data);
    }
}