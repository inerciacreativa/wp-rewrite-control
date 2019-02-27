<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class Search
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class Search extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$slug = $this->getPlugin()->getOption('wordpress.base.search');

		return <<<EOT

# ----------------------------------------------------------------------
# Search
# ----------------------------------------------------------------------
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{QUERY_STRING} \\\\?s=([^&]+) [NC]
    RewriteRule ^$ /{$slug}/%1/? [NC,R,L]
</IfModule>

EOT;

	}

}