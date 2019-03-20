<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class RewriteSearch
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class RewriteSearch extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$slug = $this->plugin->getOption('wordpress.base.search');

		return <<<EOT

# ----------------------------------------------------------------------
# Rewrite WordPress search
# ----------------------------------------------------------------------
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{QUERY_STRING} \\\\?s=([^&]+) [NC]
    RewriteRule ^$ /{$slug}/%1/? [NC,R,L]
</IfModule>

EOT;

	}

}