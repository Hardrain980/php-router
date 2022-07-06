<?php

namespace Leo\Router\RouterException;

/**
 * Router internal exception,
 * thrown on request scheme mismatching.
 * @codeCoverageIgnore
 */
class MismatchingSchemeException extends \Exception implements RouterExceptionInterface
{
	private string $received;
	private string $expected;

	public function __construct(string $received, string $expected)
	{
		$this->received = $received;
		$this->expected = $expected;
	}

	public function getErrorMessage(): string
	{
		return "Invalid scheme \"{$this->received}\", expecting \"{$this->expected}\"";
	}

	public function getContext(): array
	{
		return [
			'received_scheme' => $this->received,
			'expected_scheme' => $this->expected,
		];
	}
}

?>
