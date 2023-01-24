<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class Protect
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class FileAccess extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public static function initial(): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# File access
# ----------------------------------------------------------------------
# Block access to directories without a default document.
<IfModule mod_autoindex.c>
    Options -Indexes
</IfModule>

# Block access to all hidden files and directories with the exception of
# the visible content from within the `/.well-known/` hidden directory.
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} "!(^|/)\.well-known/([^./]+./?)+$" [NC]
    RewriteCond %{SCRIPT_FILENAME} -d [OR]
    RewriteCond %{SCRIPT_FILENAME} -f
    RewriteRule "(^|/)\." - [F]
</IfModule>

# Block access to files that can expose sensitive information.
<IfModule mod_authz_core.c>
    <FilesMatch "(^#.*#|\.(bak|conf|dist|fla|in[ci]|log|orig|psd|sh|sql|sw[op])|~)|(^(composer|package|yarn)\.(json|lock))$">
        Require all denied
    </FilesMatch>
</IfModule>

EOT;
	}

}
