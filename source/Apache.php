<?php

namespace ic\Plugin\RewriteControl;

use ic\Plugin\RewriteControl\Apache\Base;
use ic\Plugin\RewriteControl\Apache\Charset;
use ic\Plugin\RewriteControl\Apache\CORS;
use ic\Plugin\RewriteControl\Apache\CSP;
use ic\Plugin\RewriteControl\Apache\Deflate;
use ic\Plugin\RewriteControl\Apache\Expires;
use ic\Plugin\RewriteControl\Apache\FeedBurner;
use ic\Plugin\RewriteControl\Apache\FileAccess;
use ic\Plugin\RewriteControl\Apache\HSTS;
use ic\Plugin\RewriteControl\Apache\IE;
use ic\Plugin\RewriteControl\Apache\MIME;
use ic\Plugin\RewriteControl\Apache\Root;
use ic\Plugin\RewriteControl\Apache\Search;
use ic\Plugin\RewriteControl\Apache\ServiceWorker;
use ic\Plugin\RewriteControl\Apache\SSL;
use ic\Plugin\RewriteControl\Apache\WWW;
use ic\Plugin\RewriteControl\Apache\XContentType;
use ic\Plugin\RewriteControl\Apache\XFrame;

/**
 * Class Apache
 *
 * @package ic\Plugin\RewriteControl
 */
class Apache
{

	/**
	 * @var RewriteControl
	 */
	private $plugin;

	/**
	 * @var array
	 */
	private static $configOptions = [
		'cors'          => true,
		'ie'            => true,
		'mime'          => true,
		'charset'       => true,
		'deflate'       => true,
		'expires'       => true,
		'serviceworker' => '',

		'ssl'        => true,
		'www'        => true,
		'search'     => true,
		'feedburner' => '',

		'xframe'       => false,
		'csp'          => '',
		'fileaccess'   => true,
		'hsts'         => [
			'enable'     => false,
			'subdomains' => false,
			'preload'    => false,
		],
		'xcontenttype' => true,
	];

	/**
	 * @var array
	 */
	private static $configClasses = [
		'cors'          => CORS::class,
		'ie'            => IE::class,
		'mime'          => MIME::class,
		'charset'       => Charset::class,
		'deflate'       => Deflate::class,
		'expires'       => Expires::class,
		'root'          => Root::class,
		'serviceworker' => ServiceWorker::class,

		'ssl'        => SSL::class,
		'www'        => WWW::class,
		'search'     => Search::class,
		'feedburner' => FeedBurner::class,

		'xframe'       => XFrame::class,
		'csp'          => CSP::class,
		'fileaccess'   => FileAccess::class,
		'hsts'         => HSTS::class,
		'xcontenttype' => XContentType::class,

		'base' => Base::class,
	];

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
	 * External constructor.
	 *
	 * @param RewriteControl $plugin
	 */
	public function __construct(RewriteControl $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * Retrieve the default options.
	 *
	 * @return array
	 */
	public function getOptions(): array
	{
		return self::$configOptions;
	}

	/**
	 * Generate Apache rules based on current configuration.
	 *
	 * @return string
	 */
	public function getDirectives(): string
	{
		return array_reduce(self::$configClasses, function ($directives, $class) {
			$instance = new $class($this->plugin);

			$directives .= $instance();

			return $directives;
		});
	}

	/**
	 * Generate .htaccess file.
	 */
	public function saveDirectives(): void
	{
		save_mod_rewrite_rules();
	}

	/**
	 * Generate a regular expression for use in several directives.
	 *
	 * @return string
	 */
	public function getFilesMatchPattern(): string
	{
		return '\.(' . implode('|', self::$filesMatchPattern) . ')$';
	}

}
