<?php

namespace ic\Plugin\RewriteControl;

use ic\Framework\Plugin\PluginClass;

/**
 * Class Frontend
 *
 * @package ic\Plugin\RewriteControl
 */
class Frontend extends PluginClass
{

	/**
	 * @inheritdoc
	 */
	protected function configure(): void
	{
		parent::configure();

		$this->hook()->on('parse_query', 'fixSearchQuery');
	}

	/**
	 * @param \WP_Query $query
	 *
	 * @return \WP_Query
	 */
	protected function fixSearchQuery(\WP_Query $query): \WP_Query
	{
		if ($query->is_search() && $this->getOption('apache.search')) {
			$query->query_vars['s'] = urldecode($query->query_vars['s']);
		}

		return $query;
	}

}