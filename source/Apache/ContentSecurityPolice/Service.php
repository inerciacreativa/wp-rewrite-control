<?php

namespace ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;

use ic\Framework\Support\Str;

/**
 * Class Service
 *
 * @package ic\Plugin\RewriteControl\Apache\ContentSecurityPolice
 */
abstract class Service
{

	protected const PREFIX = 'service.';

	/**
	 * Return the name of the class without the namespace.
	 *
	 * @return string
	 */
	public static function name(): string
	{
		static $name;

		if ($name === null) {
			$name = str_replace(__NAMESPACE__ . '\\', '', static::class);
		}

		return $name;
	}

	/**
	 * Return the name of the class in snake case.
	 *
	 * @return string
	 */
	public static function id(): string
	{
		return Str::snake(static::name());
	}

	/**
	 * Return the name of the class with spaces before capital letters.
	 *
	 * @return string
	 */
	public static function label(): string
	{
		return preg_replace('/(?<! )(?<!^)(?<![A-Z])[A-Z]/', ' $0', static::name());
	}

	/**
	 * @param array $directives
	 *
	 * @return array
	 */
	abstract public function __invoke(array $directives): array;

}