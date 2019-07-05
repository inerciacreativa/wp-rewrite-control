<?php

namespace ic\Plugin\RewriteControl;

use ic\Plugin\RewriteControl\Apache\ApacheConfig;
use ic\Plugin\RewriteControl\Apache\CacheBusting;
use ic\Plugin\RewriteControl\Apache\CacheExpiration;
use ic\Plugin\RewriteControl\Apache\CharacterEncodings;
use ic\Plugin\RewriteControl\Apache\Compression;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice;
use ic\Plugin\RewriteControl\Apache\ContentTransformation;
use ic\Plugin\RewriteControl\Apache\CrossOrigin;
use ic\Plugin\RewriteControl\Apache\ETags;
use ic\Plugin\RewriteControl\Apache\FileAccess;
use ic\Plugin\RewriteControl\Apache\InternetExplorer;
use ic\Plugin\RewriteControl\Apache\MediaTypes;
use ic\Plugin\RewriteControl\Apache\Redirection;
use ic\Plugin\RewriteControl\Apache\ReferrerPolicy;
use ic\Plugin\RewriteControl\Apache\RewriteEngine;
use ic\Plugin\RewriteControl\Apache\RewriteFeedBurner;
use ic\Plugin\RewriteControl\Apache\RewriteHttps;
use ic\Plugin\RewriteControl\Apache\RewriteSearch;
use ic\Plugin\RewriteControl\Apache\RewriteSubdomain;
use ic\Plugin\RewriteControl\Apache\ServiceWorker;
use ic\Plugin\RewriteControl\Apache\SoftwareInformation;
use ic\Plugin\RewriteControl\Apache\StrictTransportSecurity;
use ic\Plugin\RewriteControl\Apache\WordPress;
use ic\Plugin\RewriteControl\Apache\XContentType;
use ic\Plugin\RewriteControl\Apache\XFrame;
use ic\Plugin\RewriteControl\Apache\XssProtection;

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
	private static $configClasses = [
		CrossOrigin::class,
		InternetExplorer::class,
		MediaTypes::class,
		CharacterEncodings::class,

		RewriteEngine::class,
		RewriteHttps::class,
		RewriteSubdomain::class,
		RewriteSearch::class,
		RewriteFeedBurner::class,
		Redirection::class,

		XFrame::class,
		ContentSecurityPolice::class,
		FileAccess::class,
		StrictTransportSecurity::class,
		XContentType::class,
		XssProtection::class,
		ReferrerPolicy::class,
		SoftwareInformation::class,

		Compression::class,
		ContentTransformation::class,
		ETags::class,
		CacheExpiration::class,
		CacheBusting::class,
		ServiceWorker::class,
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
		return array_reduce(self::$configClasses, static function (array $options, string $class) {
			/** @var ApacheConfig $class */
			if (($value = $class::initial()) !== null) {
				$options[$class::id()] = $value;
			}

			return $options;
		}, []);
	}

	/**
	 * Generate Apache rules based on current configuration.
	 *
	 * @param string $directives
	 *
	 * @return string
	 */
	public function getDirectives(string $directives): string
	{
		$wordpress = new WordPress($this->plugin, $directives);
		$plugin    = &$this->plugin;

		return array_reduce(self::$configClasses, static function (string $directives, string $class) use ($plugin) {
				$instance = new $class($plugin);

				$directives .= $instance();

				return $directives;
			}, '') . $wordpress();
	}

	/**
	 * Generate .htaccess file.
	 */
	public function saveDirectives(): void
	{
		save_mod_rewrite_rules();
	}

}
