<?php

namespace Leo\Router\ExceptionHandler;

use Leo\Router\HttpException\HttpExceptionInterface;
use Leo\Router\RouterException\MethodNotAllowedException;
use Leo\Router\RouterException\MisdirectedRequestException;
use Leo\Router\RouterException\MismatchingSchemeException;
use Leo\Router\RouterException\RouteNotFoundException;
use Leo\Router\RouterException\RouterExceptionInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Default excepton handler handles router internal exceptions
 * and uncaught exceptions with a plaintext response,
 * but just passthrough application exceptions.
 * This is the default exception handler if no handler
 * is explicitly specified when initializing router.
 */
class DefaultExceptionHandler implements ExceptionHandlerInterface
{
	private const HEADERS = ['Content-Type' => 'text/plain; charset=utf-8'];

	/**
	 * @codeCoverageIgnore
	 */
	public function handleHttpException(HttpExceptionInterface $http_exception): ResponseInterface
	{
		throw $http_exception;
	}

	public function handleRouterExcetion(RouterExceptionInterface $router_exception): ResponseInterface
	{
		if ($router_exception instanceof MethodNotAllowedException)
			return new Response(
				status:405,
				reason:'Method Not Allowed',
				headers:self::HEADERS,
				body:$router_exception->getErrorMessage(),
			);
		elseif ($router_exception instanceof MisdirectedRequestException)
			return new Response(
				status:421,
				reason:'Misdirected Request',
				headers:self::HEADERS,
				body:$router_exception->getErrorMessage(),
			);
		elseif ($router_exception instanceof MismatchingSchemeException)
			return new Response(
				status:400,
				reason:'Bad Request',
				headers:self::HEADERS,
				body:$router_exception->getErrorMessage(),
			);
		elseif ($router_exception instanceof RouteNotFoundException)
			return new Response(
				status:404,
				reason:'Not Found',
				headers:self::HEADERS,
				body:$router_exception->getErrorMessage(),
			);
		else
			return new Response(
				status:400,
				reason:'Bad Request',
				headers:self::HEADERS,
				body:$router_exception->getErrorMessage(),
			);
	}

	public function handleUncaughtException(\Exception|\Error $exception): ResponseInterface
	{
		$body =
			"500 Internal Server Error\n".
			"The server was unable to handle your request due to internal error.";

		return new Response(
			status:500,
			reason:'Internal Server Error',
			headers:self::HEADERS,
			body:$body,
		);
	}
}

?>
