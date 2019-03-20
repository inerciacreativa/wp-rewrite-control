<?php

namespace ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;

use ic\Framework\Support\Arr;

/**
 * Class Disqus
 *
 * @package ic\Plugin\RewriteControl\Apache\ContentSecurityPolice
 */
class Disqus extends Service
{

	/**
	 * @inheritdoc
	 */
	public function __invoke(array $directives): array
	{
		Arr::push($directives['script-src'], '*.disqus.com', '*.disquscdn.com');
		Arr::push($directives['connect-src'], '*.services.disqus.com');

		return $directives;
	}

}