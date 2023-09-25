<?php

namespace Biin2013\Tiger\Foundation;

use Biin2013\PhpUtils\Arr\ArrFormat;
use Biin2013\Tiger\Exceptions\ExceptionHandler;
use Biin2013\Tiger\Foundation\Concerns\HasValidate;
use Biin2013\Tiger\Support\Response;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Route;
use Throwable;

abstract class Controller extends BaseController
{
    use HasValidate;

    protected Request $request;
    /**
     * @var array<string, mixed>
     */
    protected array $requestData;
    /**
     * @var array<mixed>|null
     */
    protected ?array $requestRouteParams;
    protected bool $validate = true;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->requestData = $this->getRequestData();
        $this->requestRouteParams = $this->getRouteParameters();
    }

    /**
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        try {
            if ($this->validate) {
                $this->validate($this->requestData, $this->requestRouteParams);
            }

            if (method_exists($this, 'run')) {
                $response = call_user_func([$this, 'run']);
            } else {
                $response = $this->getLogicHandler()($this->requestRouteParams, $this->requestData);
            }

            if ($response instanceof JsonResponse) {
                return $response;
            }

            if ($response instanceof Arrayable) {
                $response = $response->toArray();
            }

            if (!is_array($response)) {
                throw new Exception('logic response data type[' . gettype($response) . '] invalid');
            }

            return Response::success(ArrFormat::arrayKeyToCamel($response));
        } catch (Throwable $e) {
            $handler = app(ExceptionHandler::class);
            $handler->report($e);
            return $handler->render($this->request, $e);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequestData(): array
    {
        return $this->request->json()->all();
    }

    /**
     * @return array<mixed>|null
     */
    protected function getRouteParameters(): ?array
    {
        $route = $this->request->route();

        return $route instanceof Route
            ? $route->parameters()
            : null;
    }
}