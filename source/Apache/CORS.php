<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class CORS
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class CORS extends Config
{

	/**
	 * @inheritdoc
	 */
	public function getConfig(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# Cross-origin
# ----------------------------------------------------------------------
<IfModule mod_setenvif.c>
    <IfModule mod_headers.c>
        <FilesMatch "\.(bmp|cur|gif|ico|jpe?g|png|svgz?|webp)$">
            SetEnvIf Origin ":" IS_CORS
            Header set Access-Control-Allow-Origin "*" ENV=IS_CORS
        </FilesMatch>
    </IfModule>
</IfModule>

<IfModule mod_headers.c>
    <FilesMatch "\.(eot|otf|tt[cf]|woff2?)$">
        Header set Access-Control-Allow-Origin "*"
    </FilesMatch>
</IfModule>

EOT;

	}

}