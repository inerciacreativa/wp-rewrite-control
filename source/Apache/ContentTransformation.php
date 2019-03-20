<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class ContentTransformation
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class ContentTransformation extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# Content transformation
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
    Header merge Cache-Control "no-transform"
</IfModule>

EOT;

	}

}