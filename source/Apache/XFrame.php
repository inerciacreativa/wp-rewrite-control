<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class XFrame
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class XFrame extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$value   = $this->getConfig();
		$pattern = $this->getFilesMatchPattern();

		return <<<EOT

# ----------------------------------------------------------------------
# Clickjacking
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
	Header set X-Frame-Options "$value"

	<FilesMatch "$pattern">
		Header unset X-Frame-Options
	</FilesMatch>
</IfModule>

EOT;

	}

}