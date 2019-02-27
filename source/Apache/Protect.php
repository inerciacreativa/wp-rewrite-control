<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class Protect
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class Protect extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# General protection
# ----------------------------------------------------------------------
ServerSignature Off
Options -MultiViews
<IfModule mod_autoindex.c>
    Options -Indexes
</IfModule>

<IfModule mod_headers.c>
    Header unset X-Powered-By
</IfModule>

# ----------------------------------------------------------------------
# Protect files
# ----------------------------------------------------------------------
<FilesMatch "(^#.*#|\.(bak|conf|dist|in[ci]|log|psd|sh|sql|sw[op])|~)|(^(composer|package|yarn)\.(json|lock))$">
    # Apache < 2.3
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
        Satisfy All
    </IfModule>

    # Apache ≥ 2.3
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</FilesMatch>

EOT;
	}

}