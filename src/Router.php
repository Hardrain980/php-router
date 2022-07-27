<?php

namespace Leo\Router;

use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use Leo\Router\ExceptionHandler\DefaultExceptionHandler;
use Leo\Router\ExceptionHandler\ExceptionHandlerInterface;
use Leo\Router\HttpException\HttpExceptionInterface;
use Leo\Router\RouterException\MethodNotAllowedException;
use Leo\Router\RouterException\MisdirectedRequestException;
use Leo\Router\RouterException\MismatchingSchemeException;
use Leo\Router\RouterException\RouteNotFoundException;
use Leo\Router\RouterException\RouterExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Router class capsured as PSR-15 request handler
 */
class Router implements RequestHandlerInterface
{
	/**
	 * @var \FastRoute\RouteCollector Routes collection
	 */
	private RouteCollector $route_collector;

	/**
	 * @var \FastRoute\Dispatcher\GroupCountBased Router dispatcher
	 */
	private GroupCountBased $router_kernel;

	/**
	 * @var string Uri prefix inserted before path of every routes, nullable
	 */
	private ?string $prefix;

	/**
	 * @var string Valid hostname of router, nullable
	 */
	private ?string $host;

	/**
	 * @var int Valid server port of router, nullable
	 */
	private ?int $port;

	/**
	 * @var string Acceptable request scheme of router, nullable
	 */
	private ?string $scheme;

	/**
	 * @var \Leo\Router\ExceptionHandler\ExceptionHandlerInterface Exception handler
	 */
	private ExceptionHandlerInterface $exception_handler;

	/**
	 * @var string Name of router params attached to request attributes
	 */
	private string $params_attribute_name;

	public function __construct(
		?string $prefix = null,
		?string $host = null,
		?int $port = null,
		?string $scheme = null,
		?ExceptionHandlerInterface $exception_handler = null,
		string $params_attribute_name = 'ROUTER_PARAMS',
	)
	{
		$this->prefix = $prefix;
		$this->host = $host;
		$this->port = $port;
		$this->scheme = $scheme;
		$this->exception_handler = $exception_handler ?? new DefaultExceptionHandler();
		$this->route_collector = new RouteCollector(
			routeParser:new \FastRoute\RouteParser\Std(),
			dataGenerator:new \FastRoute\DataGenerator\GroupCountBased(),
		);
		$this->params_attribute_name = $params_attribute_name;
	}

	/**
	 * Handle request
	 * @param  ServerRequestInterface $request Request object
	 * @return ResponseInterface               Response object
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		try {
			$response = $this->dispatch($request);
		} catch (RouterExceptionInterface $e) {
			$response = $this->exception_handler->handleRouterExcetion($e);
		} catch (HttpExceptionInterface $e) {
			$response = $this->exception_handler->handleHttpException($e);
		} catch (\Exception|\Error $e) {
			$response = $this->exception_handler->handleUncaughtException($e);
		}

		return $response;
	}

	// @codeCoverageIgnoreStart
	public function get(string $path, RequestHandlerInterface $handler): self
	{
		return $this->addRoute($path, $handler, ['GET']);
	}

	public function post(string $path, RequestHandlerInterface $handler): self
	{
		return $this->addRoute($path, $handler, ['POST']);
	}

	public function put(string $path, RequestHandlerInterface $handler): self
	{
		return $this->addRoute($path, $handler, ['PUT']);
	}

	public function patch(string $path, RequestHandlerInterface $handler): self
	{
		return $this->addRoute($path, $handler, ['PATCH']);
	}

	public function delete(string $path, RequestHandlerInterface $handler): self
	{
		return $this->addRoute($path, $handler, ['DELETE']);
	}

	public function head(string $path, RequestHandlerInterface $handler): self
	{
		return $this->addRoute($path, $handler, ['HEAD']);
	}

	public function options(string $path, RequestHandlerInterface $handler): self
	{
		return $this->addRoute($path, $handler, ['OPTIONS']);
	}
	// @codeCoverageIgnoreEnd

	/**
	 * Add new route to router and create a new instance
	 * @param  string                  $path    Path of new route
	 * @param  RequestHandlerInterface $handler Request handler
	 * @param  array<string>           $methods Methods of new route
	 * @return self                             New router instance
	 */
	public function addRoute(
		string $path,
		RequestHandlerInterface $handler,
		array $methods
	): self
	{
		// Clone the router rather than altering the current instance,
		// Allows method chaining
		$router = clone $this;

		// Add new route
		$router->route_collector->addRoute(
			httpMethod:array_unique($methods),
			route:is_null($this->prefix) ? $path : $this->prefix . $path,
			handler:$handler,
		);

		// Create new dispatcher
		$router->router_kernel = new GroupCountBased(
			data:$router->route_collector->getData(),
		);

		// Return new router instance
		return $router;
	}

	private function dispatch(ServerRequestInterface $request): ResponseInterface
	{
		$uri = $request->getUri();
		$method = $request->getMethod();

		// Check if host is matching if host parameter is set
		if (!is_null($this->host) && $this->host !== $uri->getHost())
			throw new MisdirectedRequestException();

		// Check if port is matching if post parameter is set
		if (!is_null($this->port) && $this->port !== $uri->getPort())
			throw new MisdirectedRequestException();

		// Check if scheme is matching if scheme parameter is set
		if (!is_null($this->scheme) && $this->scheme !== $uri->getScheme())
			throw new MismatchingSchemeException(
				received:$uri->getScheme(),
				expected:$this->scheme,
			);

		$route = $this->router_kernel->dispatch(
			httpMethod:$method,
			uri:$uri->getPath()
		);
		$response = null;

		switch ($route[0]) {
			case Dispatcher::FOUND:
				$request = $request
					->withAttribute($this->params_attribute_name, $route[2]);
				$response = $route[1]->handle($request);
				break;

			case Dispatcher::METHOD_NOT_ALLOWED:
				throw new MethodNotAllowedException(method:$method);

			case Dispatcher::NOT_FOUND:
			default:
				throw new RouteNotFoundException(uri:$uri);
		}

		return $response;
	}
}

?>
