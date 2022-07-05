<?php

use Leo\Router\Cookie;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Class: Leo\Router\Cookie
 */
class CookieTest extends TestCase
{
	public function testSimpleCookieWithKeyAndValueOnly(): void
	{
		$c = new Cookie(key:'key', value:'my/value');

		$this->assertSame('key=my%2Fvalue', strval($c));
	}

	public function testRejectEmptyCookieKey(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Cookie key could not be empty');

		new Cookie(key:'',value:'my/value');
	}

	public function testCookieWithMaxAge(): void
	{
		$c = new Cookie(key:'key', value:'my/value', max_age:180);

		$this->assertSame('key=my%2Fvalue; Max-Age=180', strval($c));
	}

	public function testCookieWithPath(): void
	{
		$c = new Cookie(key:'key', value:'my/value', path:'/admin');

		$this->assertSame('key=my%2Fvalue; Path=/admin', strval($c));
	}

	public function testCookieWithDomain(): void
	{
		$c = new Cookie(key:'key', value:'my/value', domain:'domain.tld');

		$this->assertSame('key=my%2Fvalue; Domain=domain.tld', strval($c));
	}

	public function testCookieWithSecureParameter(): void
	{
		$c = new Cookie(key:'key', value:'my/value', secure:true);

		$this->assertSame('key=my%2Fvalue; Secure', strval($c));
	}

	public function testCookieWithHttpOnlyParameter(): void
	{
		$c = new Cookie(key:'key', value:'my/value', http_only:true);

		$this->assertSame('key=my%2Fvalue; HttpOnly', strval($c));
	}
}

?>
