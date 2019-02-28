<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class XSS
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class XSSProtection extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$pattern = $this->getFilesMatchPattern();

		return <<<EOT

# ----------------------------------------------------------------------
# Clickjacking
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
	Header set X-XSS-Protection "1; mode=block"

	<FilesMatch "$pattern">
		Header unset X-XSS-Protection
	</FilesMatch>
</IfModule>

EOT;

	}

}