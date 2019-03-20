<?php

namespace ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;

use ic\Framework\Support\Arr;

/**
 * Class GoogleTagManager
 *
 * @package ic\Plugin\RewriteControl\Apache\ContentSecurityPolice
 */
class GoogleTagManager extends Service
{

	/**
	 * @see https://www.simoahava.com/analytics/google-tag-manager-content-security-policy/
	 *
	 * @inheritdoc
	 */
	public function __invoke(array $directives): array
	{
		Arr::push($directives['script-src'], 'unsafe-eval', 'unsafe-inline', 'tagmanager.google.com', 'www.googletagmanager.com');
		Arr::push($directives['style-src'], 'unsafe-inline', 'tagmanager.google.com', 'fonts.googleapis.com');
		Arr::push($directives['font-src'], 'fonts.gstatic.com');

		return $directives;
	}

}