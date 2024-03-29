<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class XContentType
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class XContentType extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public static function initial(): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
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
# Reduce MIME type security risks
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
</IfModule>

EOT;
	}

}
