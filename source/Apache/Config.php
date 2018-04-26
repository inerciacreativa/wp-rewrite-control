<?php

namespace ic\Plugin\RewriteControl\Apache;

use ic\Plugin\RewriteControl\RewriteControl;

/**
 * Class Config
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
abstract class Config
{

	/**
	 * @var RewriteControl
	 */
	protected $plugin;

	/**
	 * Config constructor.
	 *
	 * @param RewriteControl $plugin
	 */
	public function __construct(RewriteControl $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		$class = strtolower(str_replace(__NAMESPACE__ . '\\', '', static::class));

		return (bool) $this->plugin->getOption("apache.$class");
	}

	/**
	 * @return string
	 */
	abstract public function getConfig(): string;

	/**
	 * @return string
	 */
	public function __invoke(): string
	{
		if ($this->isEnabled()) {
			return $this->getConfig();
		}

		return '';
	}

}