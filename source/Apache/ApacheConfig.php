<?php

namespace ic\Plugin\RewriteControl\Apache;

use ic\Plugin\RewriteControl\RewriteControl;

/**
 * Class Config
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
abstract class ApacheConfig
{

	/**
	 * @var RewriteControl
	 */
	private $plugin;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * Config constructor.
	 *
	 * @param RewriteControl $plugin
	 */
	public function __construct(RewriteControl $plugin)
	{
		$this->plugin = $plugin;
		$this->name   = strtolower(str_replace(__NAMESPACE__ . '\\', '', static::class));
	}

	/**
	 * @return RewriteControl
	 */
	public function getPlugin(): RewriteControl
	{
		return $this->plugin;
	}

	/**
	 * @return mixed
	 */
	public function getConfig()
	{
		return $this->plugin->getOption('apache.' . $this->name);
	}

	/**
	 * @return string
	 */
	public function getFilesMatchPattern(): string
	{
		return $this->plugin->getApache()->getFilesMatchPattern();
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