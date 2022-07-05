<?php

namespace Leo\Router;

/**
 * Abstraction of HTTP Cookie, immutable
 * Implements RFC2109 and RFC6265
 */
class Cookie implements \Stringable
{
	/**
	 * @var string Cookie key
	 */
	private string $key;

	/**
	 * @var string Cookie value
	 */
	private string $value;

	/**
	 * @var int Cookie lifespan, nullable
	 */
	private ?int $max_age;

	/**
	 * @var string Cookie effective path, nullable
	 */
	private ?string $path;

	/**
	 * @var string Cookie effective domain, nullable
	 */
	private ?string $domain;

	/**
	 * @var bool Only send the cookie via secure HTTPS connection
	 */
	private bool $secure;

	/**
	 * @var bool Only send the cookie via HTTP requests
	 */
	private bool $http_only;

	public function __construct(
		string $key,
		string $value,
		?int $max_age = null,
		?string $path = null,
		?string $domain = null,
		bool $secure = false,
		bool $http_only = false,
	)
	{
		if (!$key)
			throw new \UnexpectedValueException("Cookie key could not be empty");

		$this->key = $key;
		$this->value = $value;
		$this->max_age = $max_age;
		$this->path = $path;
		$this->domain = $domain;
		$this->secure = $secure;
		$this->http_only = $http_only;
	}

	public function __toString(): string
	{
		$cookie_params = [sprintf('%s=%s',
			urlencode($this->key),
			urlencode($this->value)
		)];

		if ($this->max_age !== null)
			$cookie_params[] = "Max-Age={$this->max_age}";

		if ($this->path !== null)
			$cookie_params[] = "Path={$this->path}";

		if ($this->domain !== null)
			$cookie_params[] = "Domain={$this->domain}";

		if ($this->secure == true)
			$cookie_params[] = "Secure";

		if ($this->http_only == true)
			$cookie_params[] = "HttpOnly";

		return implode('; ', $cookie_params);
	}
}

?>
