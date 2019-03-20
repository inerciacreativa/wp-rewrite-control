<?php

namespace ic\Plugin\RewriteControl\Apache;

class CacheBusting extends ApacheConfig
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
	public function getDirectives(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# Filename-based cache busting
# ----------------------------------------------------------------------
# Route all requests such as `/style.12345.css` to `/style.css` (if the file does not exists).
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.+)\.(\w+)\.(bmp|css|cur|gif|ico|jpe?g|m?js|png|svgz?|webp|webmanifest)$ $1.$3 [L]
</IfModule>

EOT;

	}

}