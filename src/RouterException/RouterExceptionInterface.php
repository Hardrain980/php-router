<?php

namespace Leo\Router\RouterException;

interface RouterExceptionInterface extends \Throwable
{
	/**
	 * Retrieve error message in natural language
	 * @return string Error message
	 */
	public function getErrorMessage(): string;

	/**
	 * Retrieve router error context
	 * @return array<string,mixed> Error context
	 */
	public function getContext(): array;
}

?>
