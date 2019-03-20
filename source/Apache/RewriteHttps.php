<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class RewriteHttps
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class RewriteHttps extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public static function initial()
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
	{
		return parent::isEnabled() && $this->plugin->hasHttps();
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# Force HTTPS
# ----------------------------------------------------------------------
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
</IfModule>

EOT;

	}

}