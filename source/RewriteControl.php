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

	protected static $base = [
		'author'              => 'author',
		'search'              => 'search',
		'comments'            => 'comments',
		'pagination'          => 'page',
		'comments_pagination' => 'comment-page',
	];

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
	 * @var Apache
	 */
	protected $apache;

	/**
	 * @var WordPress
	 */
	protected $wordpress;

	/**
	 * @inheritdoc
	 */
	protected function onCreation()
	{
		parent::onCreation();

		$this->setOptions([
			'apache'    => [
				'protect'    => true,
				'cors'       => true,
				'ie'         => true,
				'mime'       => true,
				'charset'    => true,
				'deflate'    => true,
				'expires'    => true,
				'ssl'        => true,
				'ssl_all'    => false,
				'www'        => true,
				'search'     => true,
				'feedburner' => '',
			],
			'wordpress' => [
				'base'    => self::$base,
				'archive' => [
					'category' => false,
					'tag'      => false,
					'author'   => false,
				],
			],
		]);

		$home = parse_url(home_url());

		$this->ssl  = $home['scheme'] === 'https';
		$this->www  = strpos($home['host'], 'www.') === 0;
		$this->root = isset($home['path']) ? trailingslashit($home['path']) : '/';

		$this->apache    = new Apache($this);
		$this->wordpress = new WordPress($this);
	}

	/**
	 * @inheritdoc
	 */
	protected function onInit()
	{
		$this->wordpress->setup();
	}

	/**
	 * @return bool
	 */
	public function usingSSL(): bool
	{
		return $this->ssl;
	}

	/**
	 * @return bool
	 */
	public function usingWWW(): bool
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

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	public function getBase($type): string
	{
		$base = $this->getOption("wordpress.base.$type");

		return empty($base) ? self::$base[$type] : $base;
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

}