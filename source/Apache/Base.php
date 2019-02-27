<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class Base
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class Base extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
	{
		return true;
	}

	/**
	 * @return string
	 */
	public function getConfig(): string
	{
		$root  = $this->plugin->getRoot();
		$index = $this->plugin->getIndex();

		return <<<EOT

# ----------------------------------------------------------------------
# Base
# ----------------------------------------------------------------------
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Filename based cache busting
    RewriteRule ^(.*)-[\d]{10,12}+\.(css|js)$ $1.$2 [L]

    # Multisite
    RewriteRule ^files/(.+) wp-includes/ms-files.php?file=$1 [L]

    # WordPress rewriting
    RewriteCond $1 ^(index.php)?$ [OR]
    RewriteCond $1 .(bmp|cur|gif|ico|jpe?g|png|svgz?|webp)$ [NC,OR]
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^(.*)$ - [S=1]
    RewriteRule . {$root}{$index} [L]
</IfModule>

EOT;

	}

}