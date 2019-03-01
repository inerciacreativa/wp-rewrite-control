<?php

namespace ic\Plugin\RewriteControl;

use ic\Framework\Plugin\PluginClass;
use ic\Framework\Settings\Form\Section;
use ic\Framework\Settings\Form\Tab;
use ic\Framework\Settings\Settings;
use ic\Framework\Support\Arr;

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
	 * @return string
	 */
	protected function getApacheDirectives(): string
	{
		return $this->getPlugin()->getApache()->getDirectives();
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
	 *
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	protected function initialize(): void
	{
		Settings::siteOptions($this->id(), $this->getOptions(), $this->name())
		        ->addTab('apache', function (Tab $tab) {
			        $tab->setTitle(__('Apache Config', $this->id()))
			            ->addSection('general', function (Section $section) {
				            $section->title(__('General Options', $this->id()))
				                    ->checkbox('apache.ie', __('Document modes', $this->id()), [
					                    'label' => __('Force Internet Explorer 8/9/10 to render pages in the highest document mode available.', $this->id()),
				                    ])
				                    ->checkbox('apache.mime', __('Media types', $this->id()), [
					                    'label' => __('Serve resources with the proper <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types"><code>MIME</code></a> types.', $this->id()),
				                    ])
				                    ->checkbox('apache.charset', __('Character encoding', $this->id()), [
					                    'label' => __('Serve all text resources with the media type <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Type"><code>charset</code></a> parameter to <code>UTF-8</code>', $this->id()),
				                    ])
				                    ->checkbox('apache.deflate', __('Compression', $this->id()), [
					                    'label' => __('Force compression for resources that admit it.', $this->id()),
				                    ]);
			            })
			            ->addSection('headers', function (Section $section) {
				            $section->title('Headers Options')
				                    ->checkbox('apache.expires', __('Expires headers', $this->id()), [
					                    'label' => __('Serve resources with far-future <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires"><code>Expires</code></a> headers and remove <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ETag"><code>ETag</code></a> headers.', $this->id()),
				                    ])
				                    ->checkbox('apache.cors', __('CORS headers', $this->id()), [
					                    'label' => __('Allow cross-origin for images and web fonts when browsers request it (<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS"><code>CORS</code></a>).', $this->id()),
				                    ])
				                    ->text('apache.serviceworker', __('Service worker', $this->id()), [
					                    'class'       => 'regular-text code',
					                    'description' => __('Set the scope for the service worker to the root of the site.<br>Type only the name of the script.', $this->id()),
				                    ]);
			            });

			        $tab->addSection('rewrite', function (Section $section) {
				        $section->title('Rewrite');

				        if ($this->getPlugin()->hasHttps()) {
					        $section->checkbox('apache.ssl', __('Force SSL', $this->id()), [
						        'label' => __('Always redirect to the secure <a href="https://en.wikipedia.org/wiki/HTTPS"><code>HTTPS</code></a> version.', $this->id()),
					        ]);
				        }

				        if ($this->getPlugin()->hasSubdomain()) {
					        $section->checkbox('apache.www', __('Force www', $this->id()), [
						        'label' => __('Add the <code>www</code> subdomain to the URLs.', $this->id()),
					        ]);
				        } else {
					        $section->checkbox('apache.www', __('Force non-www', $this->id()), [
						        'label' => __('Remove the <code>www</code> subdomain in the URLs.', $this->id()),
					        ]);
				        }

				        $section->checkbox('apache.search', __('Search rewrite', $this->id()), [
					        'label'       => __('Rewrite search queries <code>/?s=query</code> to permalinks <code>/search/query</code>.', $this->id()),
					        'description' => __('The search slug can be changed in the WordPress options of the plugin.', $this->id()),
				        ]);

				        $section->text('apache.feedburner', __('FeedBurner rewrite', $this->id()), [
					        'class'       => 'regular-text code',
					        'description' => __('Rewrite feed queries to FeedBurner with a <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/307"><code>HTTP 307</code></a> temporary redirection.<br>Type only the slug of the URL.', $this->id()),
				        ]);
			        });

			        $tab->addSection('security', function (Section $section) {
				        $section->title('Security');

				        $section->checkbox('apache.information', __('Server software information', $this->id()), [
					        'label' => __('Prevent Apache from adding a trailing footer line containing information about the server to the server-generated documents.', $this->id()),
				        ]);

				        $section->checkbox('apache.fileaccess', __('File protection', $this->id()), [
					        'label' => __('Block access to hidden files and directories, and files that can expose sensitive information.', $this->id()),
				        ]);

				        $section->checkbox('apache.xcontenttype', __('Reduce MIME type security risks', $this->id()), [
					        'label' => __('Prevent some browsers from MIME-sniffing the response sending the <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options"><code>X-Content-Type-Options</code></a> header with the <code>nosniff</code> value.', $this->id()),
				        ]);

				        $section->text('apache.csp', __('Content Security Police', $this->id()), [
					        'class'       => 'regular-text code',
					        'description' => __('Sets the <a href="https://content-security-policy.com/">CSP directives</a>.', $this->id()),
				        ]);

				        if ($this->getPlugin()->hasHttps()) {
					        $section->checkbox('apache.hsts.enable', __('HSTS', $this->id()), [
						        'label' => __('Enforce <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security"><code>Strict-Transport-Security</code></a> in the browser. Be aware that this, once published, is not revokable.', $this->id()),
					        ]);

					        $section->checkbox('apache.hsts.subdomains', __('HSTS on subdomains', $this->id()), [
						        'label' => __('Enable <a href="https://en.wikipedia.org/wiki/HTTPS"><code>HSTS</code></a> on all subdomains.', $this->id()),
					        ]);

					        $section->checkbox('apache.hsts.preload', __('HSTS reloading', $this->id()), [
						        'label' => __('Enable if you want to submit your site to the <a href="https://hstspreload.org/">HSTS preload service</a> maintained by Google (you also must include all subdomains).', $this->id()),
					        ]);
				        }

				        $section->choices('apache.xframe', __('Display content on frames', $this->id()), [
					        'description' => __('Configure <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options"><code>X-Frame-Options</code></a>, informing browsers not to display the content of the web page in any frame (<code>DENY</code>) or only if the origin is the same as the page itself (<code>SAMEORIGIN</code>).', $this->id()),
				        ], [false => __('Unset', $this->id()), 'DENY' => 'DENY', 'SAMEORIGIN' => 'SAMEORIGIN']);

				        $section->checkbox('apache.xssprotection', __('Enable XSS filter', $this->id()), [
					        'label' => __('Send the <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-XSS-Protection"><code>X-XSS-Protection</code></a> header to prevent web browsers from rendering the web page if a potential reflected XSS attack is detected.', $this->id()),
				        ]);
			        });

			        $tab->onFinalization(function () {
				        $this->getPlugin()->getApache()->saveDirectives();
			        });
		        })
		        ->addTab('wordpress', function (Tab $tab) {
			        if (!\defined('DOING_AJAX')) {
				        $this->getPlugin()->getWordPress()->saveRules();
			        }
			        $tab->setTitle(__('WordPress Config', $this->id()))
			            ->addSection('base', function (Section $section) {
				            $section->title(__('Custom structures', $this->id()))
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
			            })
			            ->addSection('archive', function (Section $section) {
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
			            })
			            ->onValidation(function (array $values) {
				            foreach ((array) Arr::get($values, 'wordpress.base') as $key => $value) {
					            if (empty($value)) {
						            Arr::set($values, "wordpress.base.$key", $this->getPlugin()->getWordPress()
						                                                          ->getDefaultOption($key));
					            }
				            }

				            return $values;
			            });
		        });
	}

}