<?php

namespace ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;

use ic\Framework\Support\Arr;

/**
 * Class Typekit
 *
 * @package ic\Plugin\RewriteControl\Apache\ContentSecurityPolice
 */
class Typekit extends Service
{

	/**
	 * @see https://helpx.adobe.com/fonts/using/content-security-policy.html
	 *
	 * @inheritdoc
	 */
	public function __invoke(array $directives): array
	{
		Arr::push($directives['script-src'], 'use.typekit.net');
		Arr::push($directives['style-src'], 'unsafe-inline', 'use.typekit.net');
		Arr::push($directives['img-src'], 'p.typekit.net');

		return $directives;
	}

}