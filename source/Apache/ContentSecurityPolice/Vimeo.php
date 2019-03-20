<?php

namespace ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;

use ic\Framework\Support\Arr;

/**
 * Class Vimeo
 *
 * @package ic\Plugin\RewriteControl\Apache\ContentSecurityPolice
 */
class Vimeo extends Service
{

	/**
	 * @inheritdoc
	 */
	public function __invoke(array $directives): array
	{
		Arr::push($directives['frame-src'], '*.vimeo.com');
		Arr::push($directives['script-src'], '*.vimeo.com', '*.vimeocdn.com');
		Arr::push($directives['connect-src'], '*.vimeo.com');
		Arr::push($directives['style-src'], '*.vimeocdn.com');
		Arr::push($directives['img-src'], '*.vimeocdn.com');

		return $directives;
	}

}