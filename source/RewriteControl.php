<?php

namespace ic\Plugin\RewriteControl;

use ic\Framework\Plugin\Plugin;

/**
 * Class Rewrite
 *
 * @package ic\Plugin\RewriteControl
 *
 * @see     https://github.com/h5bp/server-configs-apache
 */
class RewriteControl extends Plugin
{

	/**
	 * @var Apache
	 */
	protected $apache;

	/**
	 * @var WordPress
	 */
	protected $wordpress;

	/**
	 * @var string
	 */
	protected $root;

	/**
	 * @var bool
	 */
	protected $ssl;

	/**
	 * @var bool
	 */
	protected $www;

	/**
	 * @inheritdoc
	 */
	protected function configure(): void
	{
		parent::configure();

		$this->apache    = new Apache($this);
		$this->wordpress = new WordPress($this);

		$this->setOptions([
			'apache'    => $this->apache->getOptions(),
			'wordpress' => $this->wordpress->getOptions(),
		]);

		$home = parse_url(home_url());

		$this->ssl  = $home['scheme'] === 'https';
		$this->www  = strpos($home['host'], 'www.') === 0;
		$this->root = isset($home['path']) ? trailingslashit($home['path']) : '/';
	}

	/**
	 * @inheritdoc
	 */
	protected function initialize(): void
	{
		$this->wordpress->initialize();
	}

	/**
	 * @return Apache
	 */
	public function getApache(): Apache
	{
		return $this->apache;
	}

	/**
	 * @return WordPress
	 */
	public function getWordPress(): WordPress
	{
		return $this->wordpress;
	}

	/**
	 * @return bool
	 */
	public function hasHttps(): bool
	{
		return $this->ssl;
	}

	/**
	 * @return bool
	 */
	public function hasSubdomain(): bool
	{
		return $this->www;
	}

	/**
	 * @return bool
	 */
	public function usingRewrite(): bool
	{
		global $wp_rewrite;

		return $wp_rewrite->using_mod_rewrite_permalinks();
	}

	/**
	 * @return string
	 */
	public function getRoot(): string
	{
		return $this->root;
	}

	/**
	 * @return string
	 */
	public function getIndex(): string
	{
		global $wp_rewrite;

		return $wp_rewrite->index;
	}

}
