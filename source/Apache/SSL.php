<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class SSL
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class SSL extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
	{
		return parent::isEnabled() && $this->getPlugin()->usingSSL();
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# SSL
# ----------------------------------------------------------------------
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
</IfModule>

EOT;

	}

}