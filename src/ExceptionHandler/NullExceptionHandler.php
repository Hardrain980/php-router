<?php

namespace Leo\Router\ExceptionHandler;

use Leo\Router\HttpException\HttpExceptionInterface;
use Leo\Router\RouterException\RouterExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Null exception handler does nothing but pass the exceptions
 * being thrown.
 * Useful for debug purposes.
 * @codeCoverageIgnore
 */
class NullExceptionHandler implements ExceptionHandlerInterface
{
	public function handleHttpException(HttpExceptionInterface $http_exception): ResponseInterface
	{
		throw $http_exception;
	}

	public function handleRouterExcetion(RouterExceptionInterface $router_exception): ResponseInterface
	{
		throw $router_exception;
	}

	public function handleUncaughtException(\Exception|\Error $exception): ResponseInterface
	{
		throw $exception;
	}
}

?>
