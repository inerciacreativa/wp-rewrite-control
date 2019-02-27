<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class CSP
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class CSP extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$csp = $this->getConfig();

		return <<<EOT

# ----------------------------------------------------------------------
# Content-Security-Policy
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
	Header set Content-Security-Policy "$csp"
</IfModule>

EOT;

	}

}