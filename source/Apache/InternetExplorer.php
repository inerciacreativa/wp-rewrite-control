<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class InternetExplorer
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class InternetExplorer extends ApacheConfig
{

	use ApacheFilesMatchPattern;

	/**
	 * @inheritdoc
	 */
	public static function initial()
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$pattern = $this->getFilesMatchPattern();

		return <<<EOT

# ----------------------------------------------------------------------
# Force Internet Explorer 8/9/10 to render pages in the highest mode
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
    Header set X-UA-Compatible "IE=Edge"

    <FilesMatch "$pattern">
        Header unset X-UA-Compatible
    </FilesMatch>
</IfModule>

EOT;

	}

}