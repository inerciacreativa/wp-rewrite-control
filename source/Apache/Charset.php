<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class Charset
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class Charset extends Config
{

	/**
	 * @inheritdoc
	 */
	public function getConfig(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# UTF-8 encoding
# ----------------------------------------------------------------------
AddDefaultCharset utf-8

<IfModule mod_mime.c>
    AddCharset utf-8 .atom \
                     .bbaw \
                     .css \
                     .geojson \
                     .js \
                     .json \
                     .jsonld \
                     .manifest \
                     .rdf \
                     .rss \
                     .topojson \
                     .vtt \
                     .webapp \
                     .webmanifest \
                     .xloc \
                     .xml
</IfModule>

EOT;

	}

}