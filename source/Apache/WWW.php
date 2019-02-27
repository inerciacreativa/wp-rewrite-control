<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class WWW
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class WWW extends ApacheConfig
{

	protected function add(): string
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

	protected function remove(): string
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

	/**
	 * @return string
	 */
	public function getDirectives(): string
	{
		return $this->getPlugin()->hasSubdomain() ? $this->add() : $this->remove();
	}

}