<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class MediaTypes
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class MediaTypes extends ApacheConfig
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
# Add MIME types
# ----------------------------------------------------------------------
<IfModule mod_mime.c>
  # Data interchange
    AddType application/atom+xml                        atom
    AddType application/json                            json map topojson
    AddType application/ld+json                         jsonld
    AddType application/rss+xml                         rss
    AddType application/geo+json                        geojson
    AddType application/rdf+xml                         rdf
    AddType application/xml                             xml

  # JavaScript
    AddType text/javascript                             js

  # Manifest files
    AddType application/manifest+json                   webmanifest
    AddType application/x-web-app-manifest+json         webapp
    AddType text/cache-manifest                         appcache

  # Media files
    AddType audio/mp4                                   f4a f4b m4a
    AddType audio/ogg                                   oga ogg opus
    AddType image/bmp                                   bmp
    AddType image/svg+xml                               svg svgz
    AddType image/webp                                  webp
    AddType video/mp4                                   f4v f4p m4v mp4
    AddType video/ogg                                   ogv
    AddType video/webm                                  webm
    AddType video/x-flv                                 flv
    AddType image/x-icon                                cur ico

  # WebAssembly
    AddType application/wasm                            wasm

  # Web fonts
    AddType font/woff                                   woff
    AddType font/woff2                                  woff2
    AddType application/vnd.ms-fontobject               eot
    AddType font/ttf                                    ttf
    AddType font/collection                             ttc
    AddType font/otf                                    otf

  # Other
    AddType application/octet-stream                    safariextz
    AddType application/x-bb-appworld                   bbaw
    AddType application/x-chrome-extension              crx
    AddType application/x-opera-extension               oex
    AddType application/x-xpinstall                     xpi
    AddType text/calendar                               ics
    AddType text/markdown                               markdown md
    AddType text/vcard                                  vcard vcf
    AddType text/vnd.rim.location.xloc                  xloc
    AddType text/vtt                                    vtt
    AddType text/x-component                            htc
</IfModule>

EOT;

	}

}
