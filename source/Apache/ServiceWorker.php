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
	</IfModule>
</FilesMatch>

EOT;

	}

}