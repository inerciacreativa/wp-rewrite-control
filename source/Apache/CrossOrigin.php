<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class CrossOrigin
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class CrossOrigin extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public static function initial(): array
	{
		return [
			'images' => true,
			'fonts'  => true,
			'timing' => false,
		];
	}

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
	{
		return $this->getConfig('images') || $this->getConfig('fonts') || $this->getConfig('timing');
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$directives = implode("\n", array_filter([
			$this->getConfig('images') ? $this->getImagesDirective() : null,
			$this->getConfig('fonts') ? $this->getFontsDirective() : null,
			$this->getConfig('timing') ? $this->getTimingDirective() : null,
		]));

		return <<<EOT

# ----------------------------------------------------------------------
# Cross-origin
# ----------------------------------------------------------------------
$directives
EOT;

	}

	/**
	 * @return string
	 */
	private function getImagesDirective(): string
	{
		return <<<EOT
# Allow cross-origin access to images.
<IfModule mod_setenvif.c>
    <IfModule mod_headers.c>
        <FilesMatch "\.(bmp|cur|gif|ico|jpe?g|png|svgz?|webp)$">
            SetEnvIf Origin ":" IS_CORS
            Header set Access-Control-Allow-Origin "*" ENV=IS_CORS
        </FilesMatch>
    </IfModule>
</IfModule>

EOT;
	}

	/**
	 * @return string
	 */
	private function getFontsDirective(): string
	{
		return <<<EOT
# Allow cross-origin access to web fonts.
<IfModule mod_headers.c>
    <FilesMatch "\.(eot|otf|tt[cf]|woff2?)$">
        Header set Access-Control-Allow-Origin "*"
    </FilesMatch>
</IfModule>

EOT;
	}

	/**
	 * @return string
	 */
	private function getTimingDirective(): string
	{
		return <<<EOT
# Allow cross-origin access to the timing information for all resources.
<IfModule mod_headers.c>
    Header set Timing-Allow-Origin: "*"
</IfModule>

EOT;
	}

}
