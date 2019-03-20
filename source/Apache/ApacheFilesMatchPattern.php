<?php

namespace ic\Plugin\RewriteControl\Apache;

trait ApacheFilesMatchPattern
{

	/**
	 * @var array
	 */
	private static $filesMatchPattern = [
		'appcache',
		'atom',
		'bbaw',
		'bmp',
		'br',
		'crx',
		'css',
		'cur',
		'eot',
		'f4[abpv]',
		'flv',
		'geojson',
		'gif',
		'gz',
		'htc',
		'ic[os]',
		'jpe?g',
		'm?js',
		'json(ld)?',
		'm4[av]',
		'manifest',
		'map',
		'markdown',
		'md',
		'mp4',
		'oex',
		'og[agv]',
		'opus',
		'otf',
		'pdf',
		'png',
		'rdf',
		'rss',
		'safariextz',
		'svgz?',
		'swf',
		'topojson',
		'tt[cf]',
		'txt',
		'vcard',
		'vcf',
		'vtt',
		'wasm',
		'webapp',
		'web[mp]',
		'webmanifest',
		'woff2?',
		'xloc',
		'xml',
		'xpi',
	];

	/**
	 * Generate a regular expression for use in several directives.
	 *
	 * @return string
	 */
	public function getFilesMatchPattern(): string
	{
		return '\.(' . implode('|', static::$filesMatchPattern) . ')$';
	}

}