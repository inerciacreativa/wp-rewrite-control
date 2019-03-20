<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class CharacterEncodings
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class CharacterEncodings extends ApacheConfig
{

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
		return <<<EOT

# ----------------------------------------------------------------------
# UTF-8 encoding
# ----------------------------------------------------------------------
AddDefaultCharset utf-8

<IfModule mod_mime.c>
    AddCharset utf-8 .appcache \
                     .bbaw \
                     .css \
                     .htc \
                     .ics \
                     .js \
                     .json \
                     .manifest \
                     .map \
                     .markdown \
                     .md \
                     .mjs \
                     .topojson \
                     .vtt \
                     .vcard \
                     .vcf \
                     .webmanifest \
                     .xloc
</IfModule>

EOT;

	}

}