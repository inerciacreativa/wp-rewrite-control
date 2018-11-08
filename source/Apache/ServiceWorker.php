<?php

namespace ic\Plugin\RewriteControl\Apache;

class ServiceWorker extends Config
{

	/**
	 * @inheritdoc
	 */
	public function getConfig(): string
	{
		$script = $this->plugin->getOption('apache.serviceworker');
		$file   = pathinfo($script);
		$regexp = $file['filename'] . '(_[a-f\d]+)?\.' . $file['extension'];

		return <<<EOT

# ----------------------------------------------------------------------
# Service Worker
# ----------------------------------------------------------------------
<FilesMatch "$regexp">
	<IfModule mod_headers.c>
		Header set Service-Worker-Allowed "/"
		Header set Cache-Control "max-age=0, no-cache, no-store, must-revalidate"
		Header set Pragma "no-cache"
		Header set Expires "Wed, 11 Jan 1984 05:00:00 GMT"
	</IfModule>
</FilesMatch>

EOT;

	}

}