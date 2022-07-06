<?php

namespace Leo\Router\RouterException;

/**
 * Router internal exception,
 * thrown on request hostname or port mismatching.
 * @codeCoverageIgnore
 */
class MisdirectedRequestException extends \Exception implements RouterExceptionInterface
{
	public function getErrorMessage(): string
	{
		return "The requested site is not hosted on this server";
	}

	public function getContext(): array
	{
		return [];
	}
}

?>
