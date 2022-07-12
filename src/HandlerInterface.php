<?php

namespace Leo\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface for request handler callbacks
 */
interface HandlerInterface
{
	/**
	 * Handle server requested passed by router
	 * @param  ServerRequestInterface $request Server request object
	 * @param  array<string,string>   $params  URI variable parameters
	 * @return ResponseInterface               Response object
	 */
	public function __invoke(ServerRequestInterface $request, array $params):ResponseInterface;
}

?>
