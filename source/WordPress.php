<?php

namespace ic\Plugin\RewriteControl;

/**
 * Class WordPress
 *
 * @package ic\Plugin\RewriteControl
 */
class WordPress
{

	/**
	 * @var RewriteControl
	 */
	protected $plugin;

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
	public function setup(): void
	{
		global $wp_rewrite;

		$wp_rewrite->author_base              = $this->plugin->getBase('author');
		$wp_rewrite->search_base              = $this->plugin->getBase('search');
		$wp_rewrite->comments_base            = $this->plugin->getBase('comments');
		$wp_rewrite->pagination_base          = $this->plugin->getBase('pagination');
		$wp_rewrite->comments_pagination_base = $this->plugin->getBase('comments_pagination');
	}

	/**
	 * @param array $rules
	 *
	 * @return array
	 */
	public function rules(array $rules): array
	{
		$pagination = $this->plugin->getBase('pagination');

		if ($this->plugin->getOption('wordpress.archive.category')) {
			$base  = get_option('category_base', 'category');
			$rules = array_merge($this->getArchiveRules('category_name', $base, $pagination), $rules);
		}

		if ($this->plugin->getOption('wordpress.archive.tag')) {
			$base  = get_option('tag_base', 'tag');
			$rules = array_merge($this->getArchiveRules('tag', $base, $pagination), $rules);
		}

		if ($this->plugin->getOption('wordpress.archive.author')) {
			$base  = $this->plugin->getBase('author');
			$rules = array_merge($this->getArchiveRules('author_name', $base, $pagination), $rules);
		}

		return $rules;
	}

	/**
	 *
	 */
	public function flush(): void
	{
		flush_rewrite_rules(false);
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