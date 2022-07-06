<?php

namespace Leo\Router\ExceptionHandler;

use Leo\Router\HttpException\HttpExceptionInterface;
use Leo\Router\RouterException\RouterExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface which exception handlers should implement
 */
interface ExceptionHandlerInterface
{
    /**
     * Handler for user-defined HTTP exceptions
     * @param  HttpExceptionInterface $http_exception HTTP exception
     * @return ResponseInterface                      Response object
     */
    public function handleHttpException(HttpExceptionInterface $http_exception): ResponseInterface;

    /**
     * Handler for router internal exceptions
     * @param  RouterExceptionInterface $router_exception Router internal exception
     * @return ResponseInterface                          Response object
     */
    public function handleRouterExcetion(RouterExceptionInterface $router_exception): ResponseInterface;

    /**
     * Handler for other exceptions not caught by the application,
     * This method avoids crashing the application when failed
     * to catch an exception.
     * @param  \Exception|\Error $exception Uncaught exception
     * @return ResponseInterface            Response object
     */
    public function handleUncaughtException(\Exception|\Error $exception): ResponseInterface;
}

?>
