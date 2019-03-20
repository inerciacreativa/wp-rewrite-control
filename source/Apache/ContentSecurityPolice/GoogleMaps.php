<?php

namespace ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;

use ic\Framework\Support\Arr;

/**
 * Class GoogleMaps
 *
 * @package ic\Plugin\RewriteControl\Apache\ContentSecurityPolice
 */
class GoogleMaps extends Service
{

	public function __invoke(array $directives): array
	{
		Arr::push($directives['script-src'], 'maps.googleapis.com');
		Arr::push($directives['img-src'], 'data:');

		return $directives;
	}

}