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
		$filesMatchPattern = $this->getFilesMatchPattern();

		return <<<EOT

# ----------------------------------------------------------------------
# Clickjacking
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
	Header set X-Frame-Options "DENY"

	<FilesMatch "$filesMatchPattern">
		Header unset X-Frame-Options
	</FilesMatch>
</IfModule>

EOT;

	}

}