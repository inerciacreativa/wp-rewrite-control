<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class ETags
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class ETags extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public static function initial()
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# ETags
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
    Header unset ETag
</IfModule>

FileETag None

EOT;

	}

}