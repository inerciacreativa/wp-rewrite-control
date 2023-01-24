<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class XSS
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class XssProtection extends ApacheConfig
{

	use ApacheFilesMatchPattern;

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
		$pattern = $this->getFilesMatchPattern();

		return <<<EOT

# ----------------------------------------------------------------------
# Reflected Cross-Site Scripting (XSS) attacks
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
