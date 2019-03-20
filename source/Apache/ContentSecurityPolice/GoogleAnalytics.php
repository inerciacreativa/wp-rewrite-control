<?php

namespace ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;

use ic\Framework\Support\Arr;

/**
 * Class GoogleAnalytics
 *
 * @package ic\Plugin\RewriteControl\Apache\ContentSecurityPolice
 */
class GoogleAnalytics extends Service
{

	/**
	 * @see https://www.bounteous.com/insights/2017/07/20/using-google-analytics-google-tag-manager-content-security-policy/
	 *
	 * @inheritdoc
	 */
	public function __invoke(array $directives): array
	{
		Arr::push($directives['script-src'], 'www.google-analytics.com');
		Arr::push($directives['img-src'], 'www.google-analytics.com', 'stats.g.doubleclick.net');
		Arr::push($directives['connect-src'], 'www.google-analytics.com', 'stats.g.doubleclick.net');

		return $directives;
	}

}