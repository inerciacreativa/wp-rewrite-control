<?php

namespace ic\Plugin\RewriteControl\Apache;

use ic\Framework\Settings\Form\Section;
use ic\Framework\Support\Arr;
use ic\Framework\Support\Str;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice\Disqus;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice\GoogleAnalytics;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice\GoogleFonts;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice\GoogleMaps;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice\GoogleTagManager;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice\Gravatar;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice\Service;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice\Typekit;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice\Vimeo;
use ic\Plugin\RewriteControl\Apache\ContentSecurityPolice\YouTube;

/**
 * Class ContentSecurityPolice
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class ContentSecurityPolice extends ApacheConfig
{

	protected static $services = [
		Typekit::class,
		GoogleFonts::class,
		Gravatar::class,
		YouTube::class,
		Vimeo::class,
		GoogleAnalytics::class,
		GoogleTagManager::class,
		GoogleMaps::class,
		Disqus::class,
	];

	protected static $fallback = [
		false     => 'None',
		'self'    => "'self'",
		'default' => 'default-src',
	];

	protected static $keywords = [
		'none',
		'self',
		'unsafe-inline',
		'unsafe-eval',
		'unsafe-hashes',
		'unsafe-allow-redirects',
		'strict-dynamic',
		'report-sample',
	];

	protected static $digest = [
		'nonce-',
		'sha256-',
		'sha384-',
		'sha512-',
	];

	protected static $sandbox = [
		'allow-forms',
		'allow-modals',
		'allow-orientation-lock',
		'allow-pointer-lock',
		'allow-popups',
		'allow-popups-to-escape-sandbox',
		'allow-presentation',
		'allow-same-origin',
		'allow-scripts',
		'allow-top-navigation',
	];

	protected static $sri = [
		'script',
		'style',
	];

	/**
	 * @inheritdoc
	 */
	public static function initial(): array
	{
		$services = array_reduce(self::$services, function (array $services, string $service) {
			/** @var Service $service */
			$services[$service::id()] = false;

			return $services;
		}, []);

		return array_merge([
			'enable'     => false,
			'fallback'   => 'self',
			'fetch'      => [
				'child-src'    => '',
				'connect-src'  => '',
				'default-src'  => "'self'",
				'font-src'     => '',
				'frame-src'    => '',
				'img-src'      => '',
				'manifest-src' => '',
				'media-src'    => '',
				'object-src'   => '',
				'prefetch-src' => '',
				'script-src'   => '',
				'style-src'    => '',
				'worker-src'   => '',
			],
			'document'   => [
				'base-uri'     => "'none'",
				'plugin-types' => '',
				'sandbox'      => [],
			],
			'navigation' => [
				'form-action'     => "'self'",
				'frame-ancestors' => "'self'",
			],
			'special'    => [
				'block-all-mixed-content'   => false,
				'upgrade-insecure-requests' => false,
				'require-sri-for'           => [],
			],
		], $services);
	}

	/**
	 * Return the available options for a field.
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public static function options(string $name): array
	{
		if (!is_array(self::$$name)) {
			return [];
		}

		if (Arr::isAssoc(self::$$name)) {
			return self::$$name;
		}

		return array_combine(self::$$name, self::$$name);
	}

	/**
	 * Adds checkboxes to enable or disable the third party services configuration.
	 *
	 * @param Section $section
	 */
	public static function services(Section $section): void
	{
		foreach (self::$services as $service) {
			$section->checkbox(self::id($service::id()), $service::label());
		}
	}

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
	{
		return (bool) $this->getConfig('enable');
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$csp = $this->getAllDirectives();

		return <<<EOT

# ----------------------------------------------------------------------
# Content-Security-Policy
# ----------------------------------------------------------------------
<IfModule mod_headers.c>
	Header set Content-Security-Policy "$csp" "expr=%{CONTENT_TYPE} == text/html"
</IfModule>

EOT;

	}

	/**
	 * @return string
	 */
	protected function getAllDirectives(): string
	{
		$directives = array_merge($this->getSpecialDirectives(), $this->getDocumentDirectives(), $this->getFetchDirectives(), $this->getNavigationDirectives());
		$fallback   = $this->getFallbackSources($directives);

		return Arr::reduce($directives, function (string $result, $values, $directive) use ($fallback) {
			if (empty($values)) {
				return $result;
			}

			if (empty($directive)) {
				return ltrim(sprintf('%s %s;', $result, $values));
			}

			// Add fallback only to fetch directives
			if ($fallback && ($directive !== 'default-src') && Str::endsWith($directive, '-src')) {
				$values = array_merge($fallback, $values);
			}

			return ltrim(sprintf('%s %s %s;', $result, $directive, implode(' ', array_unique($values))));
		}, '');
	}

	/**
	 * @return array
	 */
	protected function getDocumentDirectives(): array
	{
		return $this->parseDirectives('document');
	}

	/**
	 * @return array
	 */
	protected function getNavigationDirectives(): array
	{
		return $this->parseDirectives('navigation');
	}

	/**
	 * @return array
	 */
	protected function getSpecialDirectives(): array
	{
		$directives = [];
		$config     = $this->getConfig('special');

		if (!empty($config['require-sri-for'])) {
			$directives['require-sri-for'] = $config['require-sri-for'];
		}

		if ($config['block-all-mixed-content']) {
			$directives[] = 'block-all-mixed-content';
		}

		if ($config['upgrade-insecure-requests']) {
			$directives[] = 'upgrade-insecure-requests';
		}

		return $directives;
	}

	/**
	 * @return array
	 */
	protected function getFetchDirectives(): array
	{
		$directives = $this->parseDirectives('fetch');

		// Add the directives for the active third party services
		foreach (self::$services as $service) {
			if ($this->getConfig($service::id())) {
				$directives = (new $service)($directives);
			}
		}

		$directives['child-src'] = array_merge($directives['frame-src'], $directives['worker-src']);

		return $directives;
	}

	/**
	 * @param array $directives
	 *
	 * @return array
	 */
	protected function getFallbackSources(array $directives): array
	{
		$fallback = $this->getConfig('fallback');

		if ($fallback === 'default' && isset($directives['default-src'])) {
			$fallback = $directives['default-src'];
		} else if ($fallback === 'self') {
			$fallback = ["'self'"];
		}

		return $fallback;
	}

	/**
	 * @param string $type
	 *
	 * @return array
	 */
	protected function parseDirectives(string $type): array
	{
		$directives = $this->getConfig($type);

		return Arr::map($directives, function (string $directive, $sources) {
			return $this->parseSources($sources);
		});
	}

	/**
	 * Parses the sources and convert to an array if necessary.
	 *
	 * @param string|array $sources
	 * @param bool         $quote
	 *
	 * @return array
	 */
	protected function parseSources($sources, bool $quote = true): array
	{
		if (is_string($sources)) {
			$sources = trim($sources);
		}

		if (empty($sources)) {
			return [];
		}

		if (is_string($sources)) {
			$sources = preg_split("/\s+/", $sources);
		}

		if ($quote) {
			$sources = array_map([$this, 'maybeQuoteSource'], $sources);
		}

		return $sources;
	}

	/**
	 * Quote the sources that need it.
	 *
	 * @param string $source
	 *
	 * @return string
	 */
	protected function maybeQuoteSource(string $source): string
	{
		$source = trim($source, '"\'');

		if (in_array($source, self::$keywords, true) || Str::startsWith($source, self::$digest)) {
			return "'$source'";
		}

		return $source;
	}

}