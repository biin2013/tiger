<?php

namespace Biin2013\Tiger\Foundation\Concerns;

use Biin2013\Tiger\Foundation\Logic;
use Biin2013\Tiger\Foundation\Validation;
use Exception;
use Illuminate\Support\Str;

trait ResolveHandler
{
    private string $currentHandlerType = 'Controller';
    private string $currentHandlerTypePlural = 'Controllers';
    protected ?string $validationClass = null;
    protected ?string $logicClass = null;

    /**
     * @param string $suffix
     * @return $this
     */
    protected function setCurrentHandlerType(string $suffix = 'Logic'): static
    {
        $this->currentHandlerType = ucfirst($suffix);
        $this->currentHandlerTypePlural = Str::plural($this->currentHandlerType);

        return $this;
    }

    /**
     * @param string $type
     * @return string
     * @throws Exception
     */
    protected function getHandlerClass(string $type): string
    {
        $type = ucfirst($type);
        $typePlural = Str::plural($type);

        $path = get_class($this);
        $classes = [];
        $class = $this->getCurrentHandlerDefaultPath($path, $type, $typePlural);
        if ($class && class_exists($class)) return $class;
        $class && array_push($classes, $class);

        $routeNameClass = $this->getCurrentHandlerRouteNameClass($path, $type, $typePlural);
        if (!in_array($routeNameClass, $classes)) {
            if (class_exists($routeNameClass)) return $routeNameClass;
            array_push($classes, $routeNameClass);
        }

        $routeActionClass = $this->getCurrentHandlerRouteActionClass($path, $type, $typePlural);
        if (!in_array($routeActionClass, $classes)) {
            if (class_exists($routeActionClass)) return $routeActionClass;
            array_push($classes, $routeActionClass);
        }

        throw new Exception('Target class [' . implode('] and [', $classes) . '] all does not exist');
    }

    /**
     * @return string
     */
    protected function getCurrentHandlerSeparator(): string
    {
        return '\\' . $this->currentHandlerTypePlural . '\\';
    }

    /**
     * @param string $path
     * @param string $type
     * @param string $typePlural
     * @return string
     */
    protected function getCurrentHandlerRouteNameClass(string $path, string $type, string $typePlural): string
    {
        $paths = $this->getCurrentHandlerRoutePath($path, $typePlural);
        $routeName = explode('.', request()->route()->getName());
        array_push($routeName, Str::singular(Str::studly(array_pop($routeName))));
        $paths = array_reduce($routeName, function ($c, $v) {
            array_push($c, Str::studly($v));
            return $c;
        }, $paths);

        return implode('\\', $paths) . $type;
    }

    /**
     * @param string $path
     * @param string $typePlural
     * @return array<string>
     */
    protected function getCurrentHandlerRoutePath(string $path, string $typePlural): array
    {
        $paths = explode($this->getCurrentHandlerSeparator(), $path);
        array_pop($paths);
        array_push($paths, $typePlural);

        return $paths;
    }

    /**
     * @param string $path
     * @param string $type
     * @param string $typePlural
     * @return string
     */
    protected function getCurrentHandlerRouteActionClass(string $path, string $type, string $typePlural): string
    {
        $paths = $this->getCurrentHandlerRoutePath($path, $typePlural);
        array_push($paths, 'Foundations');
        $routeName = explode('.', request()->route()->getName());
        $action = Str::singular(Str::studly(array_pop($routeName)));
        array_push($paths, $action);

        return implode('\\', $paths) . $type;
    }

    /**
     * @param string $path
     * @param string $type
     * @param string $typePlural
     * @return string
     */
    protected function getCurrentHandlerDefaultPath(string $path, string $type, string $typePlural): string
    {
        $separator = $this->getCurrentHandlerSeparator();
        if (stripos($path, $separator . 'Foundations\\')) return '';

        $paths = explode($separator, $path);
        $end = array_pop($paths);
        array_push($paths, $typePlural);

        if (str_ends_with($path, $this->currentHandlerType)) {
            $suffix = explode('\\', $end);
            $endSuffix = array_pop($suffix);
            $class = substr_replace($endSuffix, $type, strlen($endSuffix) - strlen($this->currentHandlerType));
            array_push($suffix, $class);
            $end = implode('\\', $suffix);
        }
        array_push($paths, $end);

        return implode('\\', $paths);
    }

    /**
     * @return Validation
     * @throws Exception
     */
    protected function getValidateHandler(): Validation
    {
        return app($this->validationClass ?? $this->getHandlerClass('Validation'));
    }

    /**
     * @return Logic
     * @throws Exception
     */
    protected function getLogicHandler(): Logic
    {
        return app($this->logicClass ?? $this->getHandlerClass('Logic'));
    }
}