<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class RewriteSubdomain
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class RewriteSubdomain extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public static function initial()
	{
		return true;
	}

	/**
	 * @return string
	 */
	public function getDirectives(): string
	{
		return $this->plugin->hasSubdomain() ? $this->getAddSubdomainDirective() : $this->getRemoveSubdomainDirective();
	}

	/**
	 * @return string
	 */
	private function getAddSubdomainDirective(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# Force www. subdomain
# ----------------------------------------------------------------------
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} !=on
    RewriteCond %{HTTP_HOST} !^www\. [NC]
    RewriteCond %{SERVER_ADDR} !=127.0.0.1
    RewriteCond %{SERVER_ADDR} !=::1
    RewriteRule ^ %{ENV:PROTO}://www.%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
</IfModule>

EOT;

	}

	/**
	 * @return string
	 */
	private function getRemoveSubdomainDirective(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# Force no www. subdomain
# ----------------------------------------------------------------------
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} !=on
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^ %{ENV:PROTO}://%1%{REQUEST_URI} [R=301,L]
</IfModule>

EOT;

	}

}