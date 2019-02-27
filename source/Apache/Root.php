<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class Root
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class Root extends ApacheConfig
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
		$root = $this->plugin->getRoot();

		return <<<EOT

# ----------------------------------------------------------------------
# Rewrite
# ----------------------------------------------------------------------
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase {$root}

    Options +FollowSymlinks

    RewriteCond %{HTTPS} =on
    RewriteRule ^ - [ENV=PROTO:https]
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ - [ENV=PROTO:http]
</IfModule>

EOT;

	}

}