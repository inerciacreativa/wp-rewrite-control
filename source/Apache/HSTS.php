<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class HSTS
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class HSTS extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
	{
		return (bool) $this->getConfig()['enable'] && $this->getPlugin()->hasHttps();
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$subdomains = $this->getConfig()['subdomains'] ? '; includeSubDomains' : '';
		$preload    = $this->getConfig()['preload'] ? '; preload' : '';
		$maxage     = empty($preload) ? '16070400' : '31536000';

		return <<<EOT

# ----------------------------------------------------------------------
# SSL
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
     Header set Strict-Transport-Security "max-age=$maxage$subdomains$preload" "expr=%{HTTPS} == 'on'"
 </IfModule>

EOT;
	}

}