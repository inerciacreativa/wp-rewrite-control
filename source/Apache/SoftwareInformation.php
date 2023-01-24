<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class SoftwareInformation
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class SoftwareInformation extends ApacheConfig
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
	public function getDirectives(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# Server software information
# ----------------------------------------------------------------------
# Remove the `X-Powered-By` response header that is set by some frameworks and server-side languages.
<IfModule mod_headers.c>
    Header unset X-Powered-By
</IfModule>

# Prevent Apache from adding a trailing footer line containing information about the server to the server-generated documents.
ServerSignature Off

EOT;

	}

}
