<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class ServiceWorker
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class ServiceWorker extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public static function initial(): array
	{
		return [
			'script' => '',
			'cache'  => '0',
		];
	}

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
	{
		return !empty($this->getConfig('script'));
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$script = $this->getConfig('script');
		$cache  = $this->getConfig('cache');

		$file   = pathinfo($script);
		$regexp = $file['filename'] . '(\.\w+)?\.' . $file['extension'];

		return <<<EOT

# ----------------------------------------------------------------------
# Service Worker
# ----------------------------------------------------------------------
<FilesMatch "$regexp">
    <IfModule mod_headers.c>
        Header set Service-Worker-Allowed "/"
        Header set Cache-Control "max-age=$cache, must-revalidate"
    </IfModule>
</FilesMatch>

EOT;

	}

}