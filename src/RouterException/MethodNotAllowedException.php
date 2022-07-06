<?php

namespace Leo\Router\RouterException;

/**
 * Router internal exception,
 * thrown on route found with method mismatching.
 * @codeCoverageIgnore
 */
class MethodNotAllowedException extends \Exception implements RouterExceptionInterface
{
	private string $method;

	public function __construct(string $method)
	{
		$this->method = $method;
	}

	public function getErrorMessage(): string
	{
		return "Method \"{$this->method}\" is not allowed for this route";
	}

	public function getContext(): array
	{
		return ['method' => $this->method];
	}
}

?>
