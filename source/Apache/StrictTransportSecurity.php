<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class StrictTransportSecurity
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class StrictTransportSecurity extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public static function initial()
	{
		return [
			'enable'     => false,
			'subdomains' => false,
			'preload'    => false,
		];
	}

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
	{
		return $this->getConfig('enable') && $this->plugin->hasHttps();
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$subdomains = $this->getConfig('subdomains') ? '; includeSubDomains' : '';
		$preload    = $this->getConfig('preload') ? '; preload' : '';
		$maxage     = empty($preload) ? '16070400' : '31536000';

		return <<<EOT

# ----------------------------------------------------------------------
# HTTP Strict Transport Security (HSTS)
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
    Header set Strict-Transport-Security "max-age=$maxage$subdomains$preload" "expr=%{HTTPS} == 'on'"
 </IfModule>

EOT;
	}

}