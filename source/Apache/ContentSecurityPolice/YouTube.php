<?php

namespace ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;

use ic\Framework\Support\Arr;

/**
 * Class YouTube
 *
 * @package ic\Plugin\RewriteControl\Apache\ContentSecurityPolice
 */
class YouTube extends Service
{

	/**
	 * @inheritdoc
	 */
	public function __invoke(array $directives): array
	{
		Arr::push($directives['frame-src'], 'www.youtube.com');
		Arr::push($directives['script-src'], 'www.youtube.com');

		return $directives;
	}

}