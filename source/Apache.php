<?php

namespace ic\Plugin\RewriteControl;

use ic\Plugin\RewriteControl\Apache\Base;
use ic\Plugin\RewriteControl\Apache\Charset;
use ic\Plugin\RewriteControl\Apache\CORS;
use ic\Plugin\RewriteControl\Apache\CSP;
use ic\Plugin\RewriteControl\Apache\Deflate;
use ic\Plugin\RewriteControl\Apache\Expires;
use ic\Plugin\RewriteControl\Apache\FeedBurner;
use ic\Plugin\RewriteControl\Apache\IE;
use ic\Plugin\RewriteControl\Apache\MIME;
use ic\Plugin\RewriteControl\Apache\Protect;
use ic\Plugin\RewriteControl\Apache\Root;
use ic\Plugin\RewriteControl\Apache\Search;
use ic\Plugin\RewriteControl\Apache\ServiceWorker;
use ic\Plugin\RewriteControl\Apache\SSL;
use ic\Plugin\RewriteControl\Apache\WWW;

/**
 * Class Apache
 *
 * @package ic\Plugin\RewriteControl
 */
class Apache
{

	/**
	 * @var array
	 */
	private static $config = [
		'protect'       => Protect::class,
		'cors'          => CORS::class,
		'csp'           => CSP::class,
		'ie'            => IE::class,
		'mime'          => MIME::class,
		'charset'       => Charset::class,
		'deflate'       => Deflate::class,
		'expires'       => Expires::class,
		'root'          => Root::class,
		'feedburner'    => FeedBurner::class,
		'serviceworker' => ServiceWorker::class,
		'ssl'           => SSL::class,
		'www'           => WWW::class,
		'search'        => Search::class,
		'base'          => Base::class,
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
	 * @var RewriteControl
	 */
	private $plugin;

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
	 * Generate Apache rules based on current configuration.
	 *
	 * @return string
	 */
	public function rules(): string
	{
		return array_reduce(self::$config, function ($rules, $class) {
			$instance = new $class($this->plugin);

			$rules .= $instance();

			return $rules;
		});
	}

	/**
	 * Generate .htaccess file.
	 */
	public function flush(): void
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
