<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class MIME
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class MIME extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public function getConfig(): string
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
    AddType application/vnd.geo+json                    geojson
    AddType application/xml                             rdf xml

  # JavaScript
    AddType application/javascript                      js

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

  # Web fonts
    AddType application/font-woff                       woff
    AddType application/font-woff2                      woff2
    AddType application/vnd.ms-fontobject               eot
    AddType application/x-font-ttf                      ttc ttf
    AddType font/opentype                               otf

  # Other
    AddType application/octet-stream                    safariextz
    AddType application/x-bb-appworld                   bbaw
    AddType application/x-chrome-extension              crx
    AddType application/x-opera-extension               oex
    AddType application/x-xpinstall                     xpi
    AddType text/vcard                                  vcard vcf
    AddType text/vnd.rim.location.xloc                  xloc
    AddType text/vtt                                    vtt
    AddType text/x-component                            htc
</IfModule>

# ----------------------------------------------------------------------
# Reduce MIME type security risks
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
</IfModule>

EOT;

	}

}