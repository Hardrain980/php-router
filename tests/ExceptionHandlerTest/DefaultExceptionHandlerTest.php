<?php

use Leo\Router\ExceptionHandler\DefaultExceptionHandler;
use Leo\Router\RouterException\MethodNotAllowedException;
use Leo\Router\RouterException\MisdirectedRequestException;
use Leo\Router\RouterException\MismatchingSchemeException;
use Leo\Router\RouterException\RouteNotFoundException;
use Leo\Router\RouterException\RouterExceptionInterface;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Class: Leo\Router\ExceptionHandler\DefaultExceptionHandler
 */
class DefaultExceptionHandlerTest extends TestCase
{
	private DefaultExceptionHandler $h;

	public function setUp(): void
	{
		$this->h = new DefaultExceptionHandler();
	}

	public function testHandleUncaughtException():void
	{
		$r = $this->h->handleUncaughtException(new Exception());

		$this->assertSame(500, $r->getStatusCode());
		$this->assertSame('Internal Server Error', $r->getReasonPhrase());
		$this->assertSame('text/plain; charset=utf-8', $r->getHeaderLine('Content-Type'));
	}

	/**
	 * @testdox Handle \Leo\Router\RouterException\MethodNotAllowedException
	 */
	public function testMNA():void
	{
		$r = $this->h->handleRouterExcetion(new MethodNotAllowedException('POST'));

		$this->assertSame(405, $r->getStatusCode());
		$this->assertSame('Method Not Allowed', $r->getReasonPhrase());
		$this->assertSame('text/plain; charset=utf-8', $r->getHeaderLine('Content-Type'));
		$this->assertMatchesRegularExpression('/method.*post.*allowed/i', $r->getBody());
	}

	/**
	 * @testdox Handle \Leo\Router\RouterException\MisdirectedRequestException
	 */
	public function testMR():void
	{
		$r = $this->h->handleRouterExcetion(new MisdirectedRequestException());

		$this->assertSame(421, $r->getStatusCode());
		$this->assertSame('Misdirected Request', $r->getReasonPhrase());
		$this->assertSame('text/plain; charset=utf-8', $r->getHeaderLine('Content-Type'));
	}

	/**
	 * @testdox Handle \Leo\Router\RouterException\MismatchingSchemeException
	 */
	public function testMS():void
	{
		$r = $this->h->handleRouterExcetion(new MismatchingSchemeException(
			received:'http',
			expected:'https',
		));

		$this->assertSame(400, $r->getStatusCode());
		$this->assertSame('Bad Request', $r->getReasonPhrase());
		$this->assertSame('text/plain; charset=utf-8', $r->getHeaderLine('Content-Type'));
		$this->assertMatchesRegularExpression('/scheme.*http.*https/i', $r->getBody());
	}

	/**
	 * @testdox Handle \Leo\Router\RouterException\RouteNotFoundException
	 */
	public function testRNF():void
	{
		$r = $this->h->handleRouterExcetion(new RouteNotFoundException(
			new Uri('https://domain.tld/not/exist')
		));

		$this->assertSame(404, $r->getStatusCode());
		$this->assertSame('Not Found', $r->getReasonPhrase());
		$this->assertSame('text/plain; charset=utf-8', $r->getHeaderLine('Content-Type'));
		$this->assertMatchesRegularExpression('/path.*\/not\/exist/i', $r->getBody());
	}

	/**
	 * @testdox Handle other exceptions implement \Leo\Router\RouterException\RouterExceptionInterface
	 */
	public function testCustom():void
	{
		$e = new class extends \Exception implements RouterExceptionInterface {
			public function getErrorMessage(): string
			{
				return "Error 123";
			}

			public function getContext(): array
			{
				return [];
			}
		};
		$r = $this->h->handleRouterExcetion($e);

		$this->assertSame(400, $r->getStatusCode());
		$this->assertSame('Bad Request', $r->getReasonPhrase());
		$this->assertSame('text/plain; charset=utf-8', $r->getHeaderLine('Content-Type'));
		$this->assertSame('Error 123', $r->getBody()->__toString());
	}
}

?>
