<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class SSL
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class SSL extends Config
{

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
	{
		return parent::isEnabled() && $this->plugin->usingSSL();
	}

	/**
	 * @inheritdoc
	 */
	public function getConfig(): string
	{
		$subdomains = $this->plugin->getOption('apache.ssl_all', false) ? '; includeSubDomains' : '';

		return <<<EOT

# ----------------------------------------------------------------------
# SSL
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
     Header set Strict-Transport-Security "max-age=31536000$subdomains" "expr=%{HTTPS} == 'on'"
 </IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} !=on
    RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
</IfModule>

EOT;

	}

}