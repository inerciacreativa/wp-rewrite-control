<?php

namespace ic\Plugin\RewriteControl\Apache;

use ic\Framework\Support\Str;
use ic\Plugin\RewriteControl\RewriteControl;

/**
 * Class ApacheConfig
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
abstract class ApacheConfig
{

	protected const PREFIX = 'apache';

	/**
	 * @var RewriteControl
	 */
	protected $plugin;

	/**
	 * ApacheConfig constructor.
	 *
	 * @param RewriteControl $plugin
	 */
	public function __construct(RewriteControl $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @param string $option
	 *
	 * @return string
	 */
	public static function id(string $option = null): string
	{
		static $id;

		if ($id === null) {
			$id = Str::snake(str_replace(__NAMESPACE__ . '\\', '', static::class));
		}

		return self::PREFIX . '.' . $id . ($option ? ".$option" : '');
	}

	/**
	 * @return mixed
	 */
	public static function initial()
	{
		return false;
	}

	/**
	 * @param string $option
	 *
	 * @return mixed
	 */
	public function getConfig(string $option = null)
	{
		return $this->plugin->getOption(static::id($option));
	}

	/**
	 * @return string
	 */
	abstract public function getDirectives(): string;

	/**
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return (bool) $this->getConfig();
	}

	/**
	 * @return string
	 */
	public function __invoke(): string
	{
		if ($this->isEnabled()) {
			return $this->getDirectives();
		}

		return '';
	}

}