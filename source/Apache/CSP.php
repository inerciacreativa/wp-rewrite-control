<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class CSP
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class CSP extends Config
{

	/**
	 * @inheritdoc
	 */
	public function getConfig(): string
	{
		$csp = $this->plugin->getOption('apache.csp');

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