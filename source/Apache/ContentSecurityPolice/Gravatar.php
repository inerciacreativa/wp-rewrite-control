<?php

namespace ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;

use ic\Framework\Support\Arr;

/**
 * Class Gravatar
 *
 * @package ic\Plugin\RewriteControl\Apache\ContentSecurityPolice
 */
class Gravatar extends Service
{

	/**
	 * @inheritdoc
	 */
	public function __invoke(array $directives): array
	{
		Arr::push($directives['img-src'], '*.gravatar.com');

		return $directives;
	}

}