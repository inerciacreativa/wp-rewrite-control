<?php

namespace ic\Plugin\RewriteControl;

/**
 * Class WordPress
 *
 * @package ic\Plugin\RewriteControl
 */
class WordPress
{

	protected const PREFIX = 'wordpress';

	/**
	 * @var RewriteControl
	 */
	protected $plugin;

	/**
	 * @var array
	 */
	protected static $baseOptions = [
		'author'              => 'author',
		'search'              => 'search',
		'comments'            => 'comments',
		'pagination'          => 'page',
		'comments_pagination' => 'comment-page',
	];

	/**
	 * WordPress constructor.
	 *
	 * @param RewriteControl $plugin
	 */
	public function __construct(RewriteControl $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 *
	 */
	public function initialize(): void
	{
		global $wp_rewrite;

		$wp_rewrite->author_base              = $this->getBaseOption('author');
		$wp_rewrite->search_base              = $this->getBaseOption('search');
		$wp_rewrite->comments_base            = $this->getBaseOption('comments');
		$wp_rewrite->pagination_base          = $this->getBaseOption('pagination');
		$wp_rewrite->comments_pagination_base = $this->getBaseOption('comments_pagination');
	}

	/**
	 * Retrieve the default options.
	 *
	 * @return array
	 */
	public function getOptions(): array
	{
		return [
			self::PREFIX => [
				'base'    => self::$baseOptions,
				'archive' => [
					'category' => false,
					'tag'      => false,
					'author'   => false,
				],
			],
		];
	}

	/**
	 * @param array $rules
	 *
	 * @return array
	 */
	public function getRules(array $rules): array
	{
		$pagination = $this->getBaseOption('pagination');

		if ($this->getArchiveOption('category')) {
			$base  = get_option('category_base', 'category');
			$rules = array_merge($this->getArchiveRules('category_name', $base, $pagination), $rules);
		}

		if ($this->getArchiveOption('tag')) {
			$base  = get_option('tag_base', 'tag');
			$rules = array_merge($this->getArchiveRules('tag', $base, $pagination), $rules);
		}

		if ($this->getArchiveOption('author')) {
			$base  = $this->getBaseOption('author');
			$rules = array_merge($this->getArchiveRules('author_name', $base, $pagination), $rules);
		}

		return $rules;
	}

	/**
	 *
	 */
	public function saveRules(): void
	{
		flush_rewrite_rules(false);
	}

	/**
	 * @param string $option
	 *
	 * @return string
	 */
	public function getDefaultOption(string $option): string
	{
		return self::$baseOptions[$option];
	}

	/**
	 * @param string $option
	 *
	 * @return string
	 */
	protected function getBaseOption(string $option): string
	{
		$value = $this->plugin->getOption(self::PREFIX . ".base.$option");

		return empty($value) ? $this->getDefaultOption($option) : $value;
	}

	/**
	 * @param string $option
	 *
	 * @return bool
	 */
	protected function getArchiveOption(string $option): bool
	{
		return (bool) $this->plugin->getOption(self::PREFIX . ".archive.$option");
	}

	/**
	 * @param string $type
	 * @param string $base
	 * @param string $page
	 *
	 * @return array
	 */
	protected function getArchiveRules(string $type, string $base, string $page): array
	{
		$base  = "{$base}/([^/]+)";
		$page  = "{$page}/?([0-9]{1,})";
		$year  = '([0-9]{4})';
		$month = '([0-9]{2})';
		$index = $this->plugin->getIndex() . '?' . $type;

		return [
			"{$base}/{$year}/?$"                  => $index . '=$matches[1]&year=$matches[2]',
			"{$base}/{$year}/{$page}/?$"          => $index . '=$matches[1]&year=$matches[2]&paged=$matches[3]',
			"{$base}/{$year}/{$month}/?$"         => $index . '=$matches[1]&year=$matches[2]&monthnum=$matches[3]',
			"{$base}/{$year}/{$month}/{$page}/?$" => $index . '=$matches[1]&year=$matches[2]&monthnum=$matches[3]&paged=$matches[4]',
		];
	}

}