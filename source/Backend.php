<?php

namespace ic\Plugin\RewriteControl;

use ic\Framework\Plugin\PluginClass;
use ic\Framework\Settings\Form\Section;
use ic\Framework\Settings\Form\Tab;
use ic\Framework\Settings\Settings;
use ic\Framework\Support\Arr;
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
use ic\Plugin\RewriteControl\Apache\RewriteFeedBurner;
use ic\Plugin\RewriteControl\Apache\RewriteHttps;
use ic\Plugin\RewriteControl\Apache\RewriteSearch;
use ic\Plugin\RewriteControl\Apache\RewriteSubdomain;
use ic\Plugin\RewriteControl\Apache\ServiceWorker;
use ic\Plugin\RewriteControl\Apache\SoftwareInformation;
use ic\Plugin\RewriteControl\Apache\StrictTransportSecurity;
use ic\Plugin\RewriteControl\Apache\XContentType;
use ic\Plugin\RewriteControl\Apache\XFrame;
use ic\Plugin\RewriteControl\Apache\XssProtection;

/**
 * Class Backend
 *
 * @package ic\Plugin\RewriteControl
 *
 * @method RewriteControl getPlugin()
 */
class Backend extends PluginClass
{

	/**
	 * @inheritdoc
	 */
	protected function configure(): void
	{
		parent::configure();

		$this->hook()
		     ->on('mod_rewrite_rules', 'getApacheDirectives')
		     ->on('rewrite_rules_array', 'getWordPressRules');
	}

	/**
	 * @param string $directives
	 *
	 * @return string
	 */
	protected function getApacheDirectives(string $directives): string
	{
		return $this->getPlugin()->getApache()->getDirectives($directives);
	}

	/**
	 * @param array $rules
	 *
	 * @return array
	 */
	protected function getWordPressRules(array $rules): array
	{
		return $this->getPlugin()->getWordPress()->getRules($rules);
	}

	/**
	 * @inheritdoc
	 */
	protected function initialize(): void
	{
		$settings = Settings::siteTabbedOptions($this->getOptions(), $this->name());

		$settings->tab('general', __('General', $this->id()), function (Tab $tab) {
			$tab->section('rewrite', function (Section $section) {
				$section->title(__('Rewrite', $this->id()));

				if ($this->getPlugin()->hasHttps()) {
					$section->checkbox(RewriteHttps::id(), __('Force HTTPS', $this->id()), [
						'label' => __('Always redirect to the secure <a href="https://en.wikipedia.org/wiki/HTTPS"><code>HTTPS</code></a> version.', $this->id()),
					]);
				}

				if ($this->getPlugin()->hasSubdomain()) {
					$section->checkbox(RewriteSubdomain::id(), __('Force www', $this->id()), [
						'label' => __('Add the <code>www</code> subdomain to the URLs.', $this->id()),
					]);
				} else {
					$section->checkbox(RewriteSubdomain::id(), __('Force non-www', $this->id()), [
						'label' => __('Remove the <code>www</code> subdomain in the URLs.', $this->id()),
					]);
				}

				$section->checkbox(RewriteSearch::id(), __('Search', $this->id()), [
					'label'       => __('Rewrite search queries <code>/?s=query</code> to permalinks <code>/search/query</code>.', $this->id()),
					'description' => __('The search slug can be changed in the WordPress options of the plugin.', $this->id()),
				]);

				$section->text(RewriteFeedBurner::id(), __('FeedBurner', $this->id()), [
					'class'       => 'regular-text code',
					'description' => __('Rewrite feed queries to FeedBurner with a <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/307"><code>HTTP 307</code></a> temporary redirection.<br>Type only the slug of the URL.', $this->id()),
				]);
			});

			$tab->section('performance', function (Section $section) {
				$section->title(__('Performance', $this->id()))
				        ->checkbox(Compression::id(), __('Compression', $this->id()), [
					        'label' => __('Force compression for resources that admit it.', $this->id()),
				        ])
				        ->checkbox(CacheExpiration::id(), __('Cache expiration', $this->id()), [
					        'label' => __('Serve resources with far-future <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires"><code>Expires</code></a> headers (you should use filename-based cache busting).', $this->id()),
				        ])
				        ->checkbox(ETags::id(), __('Remove ETags', $this->id()), [
					        'label' => __('Remove <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ETag"><code>ETag</code></a>, to be used with the previous setting enabled.', $this->id()),
				        ])
				        ->checkbox(ContentTransformation::id(), __('Content transformation', $this->id()), [
					        'label' => __('Prevent intermediate caches or proxies from modifying the website\'s content.', $this->id()),
				        ]);
			});

			$tab->section('cors', function (Section $section) {
				$section->title(__('Cross-Origin Resource Sharing', $this->id()))
				        ->checkbox(CrossOrigin::id('images'), __('Images', $this->id()), [
					        'label' => __('Allow cross-origin for images when browsers request it (<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS"><code>CORS</code></a>).', $this->id()),
				        ])
				        ->checkbox(CrossOrigin::id('fonts'), __('Web fonts', $this->id()), [
					        'label' => __('Allow cross-origin for web fonts.', $this->id()),
				        ])
				        ->checkbox(CrossOrigin::id('timing'), __('Resource timing', $this->id()), [
					        'label' => __('Allow cross-origin access to the timing information for all resources (<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Timing-Allow-Origin"><code>Timing-Allow-Origin</code></a>).', $this->id()),
				        ]);
			});

			$tab->section('sw', function (Section $section) {
				$section->title(__('Service Worker', $this->id()))
				        ->text(ServiceWorker::id('script'), __('Script', $this->id()), [
					        'class'       => 'regular-text code',
					        'description' => __('Set the scope for the service worker to the root of the site. Type only the filename of the script.', $this->id()),
				        ])
				        ->number(ServiceWorker::id('cache'), __('Cache time', $this->id()), [
					        'description' => __('Set the time to cache the service worker in seconds. The maximum time allowed is 1 day (86400 seconds).', $this->id()),
				        ]);
			});

			$tab->section('other', function (Section $section) {
				$section->title(__('Other', $this->id()))
				        ->checkbox(MediaTypes::id(), __('Media types', $this->id()), [
					        'label' => __('Serve resources with the proper <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types"><code>MIME</code></a> types.', $this->id()),
				        ])
				        ->checkbox(CharacterEncodings::id(), __('Character encoding', $this->id()), [
					        'label' => __('Serve all text resources with the media type <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Type"><code>charset</code></a> parameter to <code>UTF-8</code>', $this->id()),
				        ])
				        ->checkbox(InternetExplorer::id(), __('Document modes', $this->id()), [
					        'label' => __('Force Internet Explorer 8/9/10 to render pages in the highest document mode available.', $this->id()),
				        ]);
			});

			$tab->section('redirection', function (Section $section) {
				$section->title(__('Redirections', $this->id()))
				        ->textarea(Redirection::id('redirect'), 'Redirect', [
					        'class'       => 'large-text code',
					        'rows'        => 5,
					        'description' => __('The <a href="https://httpd.apache.org/docs/2.4/mod/mod_alias.html#redirect"><code>Redirect</code></a> directive maps an old URL into a new one.<br>The syntax is <code>[status] path|URL URL</code> (one per line). The default status is a <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/301"><code>HTTP 301</code></a> permanent redirection.', $this->id()),
				        ])
				        ->textarea(Redirection::id('redirect-match'), 'RedirectMatch', [
					        'class'       => 'large-text code',
					        'rows'        => 5,
					        'description' => __('The <a href="https://httpd.apache.org/docs/2.4/mod/mod_alias.html#redirectmatch"><code>RedirectMatch</code></a> directive maps an old URL into a new one, but makes use of regular expressions.<br>The syntax is <code>[status] regex URL</code> (one per line). The default status is a <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/301"><code>HTTP 301</code></a> permanent redirection.', $this->id()),
				        ]);
			});

			$tab->validation(static function (array $values) {
				$option  = ServiceWorker::id();
				$default = ServiceWorker::initial();
				$value   = Arr::get($values, $option);

				// Check for old format
				if (is_string($value)) {
					$default['script'] = $value;
					$value             = $default;
				}

				// Get the filename only
				if (!empty($value['script'])) {
					$value['script'] = pathinfo($value['script'], PATHINFO_BASENAME);
				}

				// Set the cache time in range
				$value['cache'] = (int) $value['cache'];
				if ($value['cache'] < 0) {
					$value['cache'] = 0;
				} else if ($value['cache'] > 86400) {
					$value['cache'] = 86400;
				}

				Arr::set($values, $option, $value);

				return $values;
			});

			$tab->finalization(function () {
				$this->getPlugin()->getApache()->saveDirectives();
			});
		});

		$settings->tab('security', __('Security', $this->id()), function (Tab $tab) {
			$tab->section('general', function (Section $section) {
				$section->title(__('Basic', $this->id()))
				        ->checkbox(SoftwareInformation::id(), __('Server software information', $this->id()), [
					        'label' => __('Prevent Apache from adding information about the server and server-side languages.', $this->id()),
				        ])
				        ->checkbox(FileAccess::id(), __('File protection', $this->id()), [
					        'label' => __('Block access to hidden files and directories, and files that can expose sensitive information.', $this->id()),
				        ])
				        ->checkbox(XContentType::id(), __('Reduce MIME type security risks', $this->id()), [
					        'label' => __('Prevent some browsers from MIME-sniffing the response sending the <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options"><code>X-Content-Type-Options</code></a> header with the <code>nosniff</code> value.', $this->id()),
				        ]);
			});

			if ($this->getPlugin()->hasHttps()) {
				$tab->section('hsts', function (Section $section) {
					$section->title(__('Strict-Transport-Security', $this->id()))
					        ->checkbox(StrictTransportSecurity::id('enable'), __('Enable', $this->id()), [
						        'label' => __('Enforce <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security"><code>Strict-Transport-Security</code></a> in the browser. Be aware that this, once published, <strong>is not revokable</strong>.', $this->id()),
					        ])
					        ->checkbox(StrictTransportSecurity::id('subdomains'), __('Subdomains', $this->id()), [
						        'label' => __('Enable <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security"><code>HSTS</code></a> on all subdomains.', $this->id()),
					        ])
					        ->checkbox(StrictTransportSecurity::id('preload'), __('Preload', $this->id()), [
						        'label' => __('Enable if you want to submit your site to the <a href="https://hstspreload.org/">HSTS preload service</a> maintained by Google (you also must include all subdomains).', $this->id()),
					        ]);
				});
			}

			$tab->section('advanced', function (Section $section) {
				$section->title(__('Advanced', $this->id()))
				        ->checkbox(XssProtection::id(), __('Enable XSS filter', $this->id()), [
					        'label' => __('Send the <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-XSS-Protection"><code>X-XSS-Protection</code></a> header to prevent web browsers from rendering the web page if a potential reflected XSS attack is detected.', $this->id()),
				        ])
				        ->choices(XFrame::id(), __('Display content on frames', $this->id()), [
					        'description' => __('Configure <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options"><code>X-Frame-Options</code></a>, informing browsers when to display the content of the web page in a frame.', $this->id()),
				        ], XFrame::options())
				        ->choices(ReferrerPolicy::id(), __('Referrer Policy', $this->id()), [
					        'description' => __('Configure <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy"><code>Referrer-Policy</code></a>, which governs which referrer information, sent in the <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referer"><code>Referer</code></a> header, should be included with requests made.', $this->id()),
				        ], ReferrerPolicy::options());
			});

			$tab->finalization(function () {
				$this->getPlugin()->getApache()->saveDirectives();
			});
		});

		$settings->tab('csp', __('CSP', $this->id()), function (Tab $tab) {
			$tab->section('general', function (Section $section) {
				$section->checkbox(ContentSecurityPolice::id('enable'), 'Enable', [
					'label' => __('You can enable or completely disable <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP">Content Security Police</a>.', $this->id()),
				]);
			});

			$tab->section('services', function (Section $section) {
				$section->title(__('Third-party services', $this->id()))
				        ->description(__('Configures automatically the fetch directives to allow the use of this services.', $this->id()));

				ContentSecurityPolice::services($section);
			});

			$tab->section('fetch', function (Section $section) {
				$all     = sprintf(__('<code><b>%s</b></code> to allow all sources.', $this->id()), $this->getPlugin()
				                                                                                         ->hasHttps() ? 'https:' : '*');
				$sources = __('<p>You can also use this source expressions:</p>', $this->id());
				$data    = __('<code><b>data:</b></code> to allow <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/Data_URIs"><code>data:</code> URIs</a>.', $this->id());
				$blob    = __('<code><b>blob:</b></code> to allow <a href="https://developer.mozilla.org/en-US/docs/Web/API/Blob"><code>blob:</code> URIs</a>.', $this->id());
				$media   = __('<code><b>mediastream:</b></code> to allow <a href="https://developer.mozilla.org/en-US/docs/Web/API/MediaStream_API"><code>mediastream:</code> URIs</a>.', $this->id());
				$inline  = __("<a href='https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/script-src#Unsafe_inline_script'><code><b>'unsafe-inline'</b></code></a> to allow the use of inline resources, such as inline <a href='https://developer.mozilla.org/en-US/docs/Web/HTML/Element/script'><code>&lt;script&gt;</code></a> elements, <code>javascript:</code> URIs, inline event handlers, and inline <a href='https://developer.mozilla.org/en-US/docs/Web/HTML/Element/style'><code>&lt;style&gt;</code></a> elements (<strong><i>not recommended</i></strong>).", $this->id());
				$eval    = __("<a href='https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/script-src#Unsafe_eval_expressions'><code><b>'unsafe-eval'</b></code></a> to allow the use of <code>eval()</code> and similar methods for creating code from strings (<strong><i>not recommended</i></strong>).", $this->id());
				$nonce   = __("<code><b>'nonce-&lt;<i>base64-value</i>&gt;'</b></code> to allow the use of a whitelist for specific inline <a href='https://developer.mozilla.org/en-US/docs/Web/HTML/Element/script'><code>&lt;script&gt;</code></a> and <a href='https://developer.mozilla.org/en-US/docs/Web/HTML/Element/style'><code>&lt;style&gt;</code></a> elements. The server must generate a unique nonce value each time it transmits a policy. Specifying nonce makes a modern browser ignore <code>'unsafe-inline'</code>. Be aware that <a href='https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/13246371/'>doesn't work for non-inline resources in Microsoft Edge</a>.", $this->id());
				$hash    = __("<code><b>'&lt;<i>hash-algorithm</i>&gt;-&lt;<i>base64-value</i>&gt;'</b></code>, a sha256, sha384 or sha512 hash of scripts or styles. In CSP 2.0 this applied only to inline scripts, CSP 3.0 allows it for external scripts. You can learn more about <a href='https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity'>Subresource Integrity</a> on MDN. Also, you can generate hashes for online resources with <a href='https://www.srihash.org/'>SRI Hash Generator</a>.", $this->id());
				$strict  = __("<a href='https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/script-src#strict-dynamic'><code><b>'strict-dynamic'</b></code></a> specifies that the trust explicitly given to a script present in the markup, by accompanying it with a nonce or a hash, shall be propagated to all the scripts loaded by that root script. At the same time, any whitelist or source expressions such as <code>'self'</code> or <code>'unsafe-inline'</code> will be ignored.", $this->id());

				$section->title(__('Fetch directives', $this->id()))
				        ->description(__('Fetch directives control locations from which certain resource types may be loaded. The <code>child-src</code> directive will be composed merging <code>frame-src</code> and <code>worker-src</code>.', $this->id()))
				        ->choices(ContentSecurityPolice::id('fallback'), __('Fallback', $this->id()), [
					        'description' => __('Append to the directives that are not empty.', $this->id()),
				        ], ContentSecurityPolice::options('fallback'))
				        ->text(ContentSecurityPolice::id('fetch.default-src'), 'default-src', [
					        'class'       => 'regular-text code',
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/default-src"><code>default-src</code></a> directive serves as a fallback for the other fetch directives.', $this->id()),
				        ])
				        ->textarea(ContentSecurityPolice::id('fetch.connect-src'), 'connect-src', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/connect-src"><code>connect-src</code></a> directive restricts the URLs which can be loaded using script interfaces. Applies to <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-ping"><code>&lt;a ping=""&gt;</code></a>, <a href="https://developer.mozilla.org/en-US/docs/Web/API/Fetch"><code>Fetch</code></a>, <a href="https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest"><code>XMLHttpRequest</code></a>, <a href="https://developer.mozilla.org/en-US/docs/Web/API/WebSocket"><code>WebSocket</code></a> and <a href="https://developer.mozilla.org/en-US/docs/Web/API/EventSource"><code>EventSource</code></a>.', $this->id()),
					        'append'      => __('<p>Chrome for iOS fails to render pages without a <code>\'self\'</code> policy.</p>', $this->id()),
				        ])
				        ->textarea(ContentSecurityPolice::id('fetch.font-src'), 'font-src', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/font-src"><code>font-src</code></a> directive specifies valid sources for fonts loaded using <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face"><code>@font-face</code></a>.', $this->id()),
				        ])
				        ->textarea(ContentSecurityPolice::id('fetch.frame-src'), 'frame-src', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/frame-src"><code>frame-src</code></a> directive specifies valid sources for nested browsing contexts loading using elements such as <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/frame"><code>&lt;frame&gt;</code></a> and <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/iframe"><code>&lt;iframe&gt;</code></a>.', $this->id()),
				        ])
				        ->textarea(ContentSecurityPolice::id('fetch.img-src'), 'img-src', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/img-src"><code>img-src</code></a> directive specifies valid sources of images and favicons.', $this->id()),
					        'append'      => "$sources<ul style='list-style: disc;margin-left: 2em;'><li>$all</li><li>$data</li><li>$blob</li></ul>",
				        ])
				        ->textarea(ContentSecurityPolice::id('fetch.manifest-src'), 'manifest-src', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/manifest-src"><code>manifest-src</code></a> directive specifies which <a href="https://developer.mozilla.org/en-US/docs/Web/Manifest">manifest</a> can be applied to the resource.', $this->id()),
				        ])
				        ->textarea(ContentSecurityPolice::id('fetch.media-src'), 'media-src', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/media-src"><code>media-src</code></a> directive specifies valid sources for loading media using the <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/audio"><code>&lt;audio&gt;</code></a> and <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video"><code>&lt;video&gt;</code></a> elements.', $this->id()),
					        'append'      => "$sources<ul style='list-style: disc;margin-left: 2em;'><li>$all</li><li>$data</li><li>$blob</li><li>$media</li></ul>",
				        ])
				        ->textarea(ContentSecurityPolice::id('fetch.object-src'), 'object-src', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/object-src"><code>object-src</code></a> directive specifies valid sources for the <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/object"><code>&lt;object&gt;</code></a> and <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/embed"><code>&lt;embed&gt;</code></a> elements.', $this->id()),
					        'append'      => "$sources<ul style='list-style: disc;margin-left: 2em;'><li>$all</li><li>$data</li><li>$blob</li><li>$media</li></ul>",
				        ])
				        ->textarea(ContentSecurityPolice::id('fetch.script-src'), 'script-src', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/script-src"><code>script-src</code></a> directive specifies valid sources for JavaScript.', $this->id()),
					        'append'      => "$sources<ul style='list-style: disc;margin-left: 2em;'><li>$data</li><li>$blob</li><li>$inline</li><li>$eval</li><li>$nonce</li><li>$hash</li><li>$strict</li></ul>",
				        ])
				        ->textarea(ContentSecurityPolice::id('fetch.style-src'), 'style-src', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/style-src"><code>style-src</code></a> directive specifies valid sources for stylesheets.', $this->id()),
					        'append'      => "$sources<ul style='list-style: disc;margin-left: 2em;'><li>$inline</li><li>$nonce</li><li>$hash</li></ul>",
				        ])
				        ->textarea(ContentSecurityPolice::id('fetch.worker-src'), 'worker-src', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/worker-src"><code>worker-src</code></a> for <a href="https://developer.mozilla.org/en-US/docs/Web/API/Worker"><code>Worker</code></a>, <a href="https://developer.mozilla.org/en-US/docs/Web/API/SharedWorker"><code>SharedWorker</code></a>, or <a href="https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorker"><code>ServiceWorker</code></a> scripts. If this directive is absent, the user agent will follow the <code>script-src</code> directive', $this->id()),
					        'append'      => "$sources<ul style='list-style: disc;margin-left: 2em;'><li>$data</li><li>$blob</li><li>$inline</li><li>$nonce</li><li>$hash</li><li>$strict</li></ul>",
				        ]);

			});

			$tab->section('document', function (Section $section) {
				$section->title(__('Document directives', $this->id()))
				        ->description(__('Document directives govern the properties of a document or <a href="https://developer.mozilla.org/en-US/docs/Web/API/Web_Workers_API">worker</a> environment to which a policy applies.', $this->id()))
				        ->text(ContentSecurityPolice::id('document.base-uri'), 'base-uri', [
					        'class'       => 'regular-text code',
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/base-uri"><code>base-uri</code></a> directive restricts the URLs which can be used in a document\'s <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/base"><code>&lt;base&gt;</code></a> element.', $this->id()),
					        'append'      => __('<p>Note that setting this to <code>\'self\'</code> while not intending to use the <code>&lt;base&gt;</code> element could break site functionality if an attacker manages to inject one that points to another domain.</p>', $this->id()),
				        ])
				        ->textarea(ContentSecurityPolice::id('document.plugin-types'), 'plugin-types', [
					        'class'       => 'large-text code',
					        'rows'        => 3,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/plugin-types"><code>plugin-types</code></a> directive restricts the set of plugins that can be embedded into a document by limiting the types of resources which can be loaded.', $this->id()),
				        ])
				        ->choices(ContentSecurityPolice::id('document.sandbox'), 'sandbox', [
					        'multiple'    => true,
					        'expanded'    => true,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/sandbox"><code>sandbox</code></a> directive enables a sandbox for the requested resource similar to the <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/iframe"><code>&lt;iframe&gt;</code></a> <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/iframe#attr-sandbox"><code>sandbox</code></a> attribute.', $this->id()),
				        ], ContentSecurityPolice::options('sandbox'));
			});

			$tab->section('navigation', function (Section $section) {
				$section->title(__('Navigation directives', $this->id()))
				        ->text(ContentSecurityPolice::id('navigation.form-action'), 'form-action', [
					        'class'       => 'regular-text code',
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/form-action"><code>form-action</code></a> directive restricts the URLs which can be used as the target of a form submissions from a given context.', $this->id()),
				        ])
				        ->text(ContentSecurityPolice::id('navigation.frame-ancestors'), 'frame-ancestors', [
					        'class'       => 'regular-text code',
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/frame-ancestors"><code>frame-ancestors</code></a> directive specifies valid parents that may embed a page using <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/frame"><code>&lt;frame&gt;</code></a>, <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/iframe"><code>&lt;iframe&gt;</code></a>, <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/object"><code>&lt;object&gt;</code></a> or <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/embed"><code>&lt;embed&gt;</code></a> elements.', $this->id()),
					        'append'      => __("<p>Setting this directive to <code>'none'</code> is similar to <code>X-Frame-Options: deny</code> (which is also supported in older browsers), and can be set in the Security tab.</p>", $this->id()),
				        ]);
			});

			$tab->section('special', function (Section $section) {
				$section->title(__('Special directives', $this->id()))
				        ->description(__('This directives are extensions to CSP defined by other specifications.', $this->id()))
				        ->choices(ContentSecurityPolice::id('special.require-sri-for'), 'require-sri-for', [
					        'multiple'    => true,
					        'expanded'    => true,
					        'description' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/require-sri-for"><code>require-sri-for</code></a> directive requires the use of <a href="https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity"><abbr title="Subresource Integrity">SRI</abbr></a> for scripts and/or styles on the page.', $this->id()),
				        ], ContentSecurityPolice::options('sri'))
				        ->checkbox(ContentSecurityPolice::id('special.upgrade-insecure-requests'), 'upgrade-insecure-requests', [
					        'label' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/upgrade-insecure-requests"><code>upgrade-insecure-requests</code></a> directive instructs user agents to treat all of a site\'s insecure URLs (those served over HTTP) as though they have been replaced with secure URLs (those served over HTTPS).', $this->id()),
				        ])
				        ->checkbox(ContentSecurityPolice::id('special.block-all-mixed-content'), 'block-all-mixed-content', [
					        'label' => __('The <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/block-all-mixed-content"><code>block-all-mixed-content</code></a> directive prevents loading any assets using HTTP when the page is loaded using HTTPS.', $this->id()),
				        ]);
			});

			$tab->finalization(function () {
				$this->getPlugin()->getApache()->saveDirectives();
			});
		});

		$settings->tab('wordpress', __('WordPress', $this->id()), function (Tab $tab) {
			if (!defined('DOING_AJAX')) {
				$this->getPlugin()->getWordPress()->saveRules();
			}
			$tab->section('base', function (Section $section) {
				$section->title(__('Custom structures', $this->id()))
				        ->description('Change the slugs for common structures (leave blank for defaults).')
				        ->text('wordpress.base.author', __('Author base', $this->id()), [
					        'class' => 'regular-text code',
				        ])
				        ->text('wordpress.base.search', __('Search base', $this->id()), [
					        'class' => 'regular-text code',
				        ])
				        ->text('wordpress.base.comments', __('Comments base', $this->id()), [
					        'class' => 'regular-text code',
				        ])
				        ->text('wordpress.base.pagination', __('Pagination base', $this->id()), [
					        'class' => 'regular-text code',
				        ])
				        ->text('wordpress.base.comments_pagination', __('Comments pagination base', $this->id()), [
					        'class' => 'regular-text code',
				        ]);
			})->section('archive', function (Section $section) {
				$section->title(__('Archives', $this->id()))
				        ->checkbox('wordpress.archive.category', __('Categories', $this->id()), [
					        'label' => __('Enable archives for categories.', $this->id()),
				        ])
				        ->checkbox('wordpress.archive.tag', __('Tags', $this->id()), [
					        'label' => __('Enable archives for tags.', $this->id()),
				        ])
				        ->checkbox('wordpress.archive.author', __('Authors', $this->id()), [
					        'label' => __('Enable archives for authors.', $this->id()),
				        ]);
			})->validation(function (array $values) {
				foreach ((array) Arr::get($values, 'wordpress.base') as $key => $value) {
					if (empty($value)) {
						Arr::set($values, "wordpress.base.$key", $this->getPlugin()
						                                              ->getWordPress()
						                                              ->getDefaultOption($key));
					}
				}

				return $values;
			});
		});
	}

}