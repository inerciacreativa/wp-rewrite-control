<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class Referrer
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class ReferrerPolicy extends ApacheConfig
{

	use ApacheFilesMatchPattern;

	/**
	 * @inheritdoc
	 */
	public static function initial(): string
	{
		return 'no-referrer-when-downgrade';
	}

	/**
	 * @return array
	 */
	public static function options(): array
	{
		return [
			false                             => 'Not set',
			'no-referrer'                     => 'no-referrer',
			'no-referrer-when-downgrade'      => 'no-referrer-when-downgrade',
			'origin'                          => 'origin',
			'origin-when-cross-origin'        => 'origin-when-cross-origin',
			'same-origin'                     => 'same-origin',
			'strict-origin'                   => 'strict-origin',
			'strict-origin-when-cross-origin' => 'strict-origin-when-cross-origin',
			'unsafe-url'                      => 'unsafe-url',
		];
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$value   = $this->getConfig();
		$pattern = $this->getFilesMatchPattern();

		return <<<EOT

# ----------------------------------------------------------------------
# Referrer Policy
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
    Header set Referrer-Policy "$value"

    <FilesMatch "$pattern">
        Header unset Referrer-Policy
    </FilesMatch>
</IfModule>

EOT;

	}

}
