<?php

use Leo\Router\HandlerInterface;
use Leo\Router\HttpException\HttpExceptionInterface;
use Leo\Router\Router;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @testdox Class: \Leo\Router\Router
 */
class RouterTest extends TestCase
{
	/**
	 * @testdox Return HTTP 421 on mismatching hostname
	 */
	public function testMH(): void
	{
		$h = new class implements RequestHandlerInterface {
			public function handle(ServerRequestInterface $request): ResponseInterface
			{
				return new Response(status:200, body:'Yeah!');
			}
		};

		$r = (new Router(host:'domain.tld'))->get(path:'/', handler:$h);

		$rs = $r->handle(new ServerRequest(method:'GET', uri:'http://hacker.tld/'));

		$this->assertSame(421, $rs->getStatusCode());
	}

	/**
	 * @testdox Return HTTP 421 on mismatching server port
	 */
	public function testMP(): void
	{
		$h = new class implements RequestHandlerInterface {
			public function handle(ServerRequestInterface $request): ResponseInterface
			{
				return new Response(status:200, body:'Yeah!');
			}
		};

		$r = (new Router(port:8080))->get(path:'/', handler:$h);

		$rs = $r->handle(new ServerRequest(method:'GET', uri:'http://domain.tld:9090/'));

		$this->assertSame(421, $rs->getStatusCode());
	}

	/**
	 * @testdox Return HTTP 400 on mismatching scheme
	 */
	public function testMS(): void
	{
		$h = new class implements RequestHandlerInterface {
			public function handle(ServerRequestInterface $request): ResponseInterface
			{
				return new Response(status:200, body:'Yeah!');
			}
		};

		$r = (new Router(scheme:'https'))->get(path:'/', handler:$h);

		$rs = $r->handle(new ServerRequest(method:'GET', uri:'http://domain.tld/'));

		$this->assertSame(400, $rs->getStatusCode());
	}

	public function testCallHandlerForUncaughtException(): void
	{
		$h = new class implements RequestHandlerInterface {
			public function handle(ServerRequestInterface $request): ResponseInterface
			{
				throw new \Exception();
			}
		};

		$r = (new Router())->get(path:'/', handler:$h);

		$rs = $r->handle(new ServerRequest(method:'GET', uri:'http://domain.tld/'));

		$this->assertSame(500, $rs->getStatusCode());
	}

	public function testCallHandlerForApplicationException(): void
	{
		$this->expectException(HttpExceptionInterface::class);

		$h = new class implements RequestHandlerInterface {
			public function handle(ServerRequestInterface $request): ResponseInterface
			{
				throw new class extends \Exception implements \Leo\Router\HttpException\HttpExceptionInterface {};
			}
		};

		$r = (new Router())->get(path:'/', handler:$h);

		$r->handle(new ServerRequest(method:'GET', uri:'http://domain.tld/'));
	}

	/**
	 * @testdox Return HTTP 405 when route is found but method is mismatching
	 */
	public function testMNA(): void
	{
		$h = new class implements RequestHandlerInterface {
			public function handle(ServerRequestInterface $request): ResponseInterface
			{
				return new Response(status:200, body:'Yeah!');
			}
		};

		$r = (new Router())->get(path:'/', handler:$h);

		$rs = $r->handle(new ServerRequest(method:'PUT', uri:'http://domain.tld/'));

		$this->assertSame(405, $rs->getStatusCode());
	}

	/**
	 * @testdox Return HTTP 404 when route could not be found
	 */
	public function testRNF(): void
	{
		$h = new class implements RequestHandlerInterface {
			public function handle(ServerRequestInterface $request): ResponseInterface
			{
				return new Response(status:200, body:'Yeah!');
			}
		};

		$r = (new Router())->get(path:'/', handler:$h);

		$rs = $r->handle(new ServerRequest(method:'GET', uri:'http://domain.tld/not/exist'));

		$this->assertSame(404, $rs->getStatusCode());
	}

	public function testHandleRequestsNormally(): void
	{
		$h = new class implements RequestHandlerInterface {
			public function handle(ServerRequestInterface $request): ResponseInterface
			{
				return new Response(status:200, body:'Yeah!');
			}
		};

		$r = (new Router())
			->get('/', $h);

		$this->assertSame(200,
			$r->handle(new ServerRequest(
				method:'GET',
				uri:'http://domain.tld/',
			))->getStatusCode()
		);

		$this->assertSame('Yeah!',
			$r->handle(new ServerRequest(
				method:'GET',
				uri:'http://domain.tld/',
			))->getBody()->__toString()
		);
	}
}

?>
