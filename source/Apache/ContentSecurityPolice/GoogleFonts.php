<?php

namespace ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;

use ic\Framework\Support\Arr;

/**
 * Class GoogleFonts
 *
 * @package ic\Plugin\RewriteControl\Apache\ContentSecurityPolice
 */
class GoogleFonts extends Service
{

	/**
	 * @inheritdoc
	 */
	public function __invoke(array $directives): array
	{
		Arr::push($directives['style-src'], 'fonts.googleapis.com');
		Arr::push($directives['font-src'], 'fonts.gstatic.com');

		return $directives;
	}

}