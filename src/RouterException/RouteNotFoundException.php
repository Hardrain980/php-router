<?php

namespace Leo\Router\RouterException;

use Psr\Http\Message\UriInterface;

/**
 * Router internal exception,
 * thrown when route not found.
 * @codeCoverageIgnore
 */
class RouteNotFoundException extends \Exception implements RouterExceptionInterface
{
	private UriInterface $uri;

	public function __construct(UriInterface $uri)
	{
		$this->uri = $uri;
	}

	public function getErrorMessage(): string
	{
		return "No route to path \"{$this->uri->getPath()}\"";
	}

	public function getContext(): array
	{
		return ['uri' => $this->uri];
	}
}

?>
