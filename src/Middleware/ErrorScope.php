<?php

namespace Biin2013\Tiger\Middleware;

use Closure;
use Illuminate\Http\Request;

class ErrorScope
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string $scope
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $scope = 'common'): mixed
    {
        $request->headers->set('error-scope', $scope);

        return $next($request);
    }
}