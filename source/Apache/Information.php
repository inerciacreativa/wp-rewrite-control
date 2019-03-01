<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class Information
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class Information extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# Server software information
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
    Header unset X-Powered-By
</IfModule>

ServerSignature Off

EOT;

	}

}