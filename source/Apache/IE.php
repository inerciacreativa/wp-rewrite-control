<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class IE
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class IE extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function getConfig(): string
	{
		return <<<EOT

# ----------------------------------------------------------------------
# Force the latest IE version
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
    Header set X-UA-Compatible "IE=Edge"

    <FilesMatch "\.(appcache|atom|bbaw|bmp|crx|css|cur|eot|f4[abpv]|flv|geojson|gif|htc|ico|jpe?g|js|json(ld)?|m4[av]|manifest|map|mp4|oex|og[agv]|opus|otf|pdf|png|rdf|rss|safariextz|svgz?|swf|topojson|tt[cf]|txt|vcard|vcf|vtt|webapp|web[mp]|webmanifest|woff2?|xloc|xml|xpi)$">
        Header unset X-UA-Compatible
    </FilesMatch>
</IfModule>

EOT;

	}

}