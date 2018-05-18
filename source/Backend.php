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
		     ->on('mod_rewrite_rules', 'generateApacheRules')
		     ->on('rewrite_rules_array', 'generateWordPressRules');
	}

	/**
	 * @return string
	 */
	protected function generateApacheRules(): string
	{
		return $this->getPlugin()->getApache()->rules();
	}

	/**
	 * @param array $rules
	 *
	 * @return array
	 */
	protected function generateWordPressRules(array $rules): array
	{
		return $this->getPlugin()->getWordPress()->rules($rules);
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
				                    ->checkbox('apache.protect', __('File protection', $this->id()), [
					                    'label' => __('Block access to files that can expose sensitive information.', $this->id()),
				                    ])
				                    ->checkbox('apache.cors', __('CORS headers', $this->id()), [
					                    'label' => __('Allow cross-origin for images and web fonts when browsers request it (<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS"><code>CORS</code></a>).', $this->id()),
				                    ])
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
				                    ])
				                    ->checkbox('apache.expires', __('Expires headers', $this->id()), [
					                    'label' => __('Serve resources with far-future <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires"><code>Expires</code></a> headers and remove <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ETag"><code>ETag</code></a> headers.', $this->id()),
				                    ]);
			            })
			            ->addSection('redirect', function (Section $section) {
				            $section->title('Redirection Options');

				            if ($this->getPlugin()->usingSSL()) {
					            $section->checkbox('apache.ssl', __('Force SSL', $this->id()), [
						            'label' => __('Redirect to the secure <a href="https://en.wikipedia.org/wiki/HTTPS"><code>HTTPS</code></a> version and enforce <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security"><code>Strict Transport Security</code></a> in the browser.', $this->id()),
					            ]);
					            $section->checkbox('apache.ssl_all', __('Force SSL on all subdomains', $this->id()), [
						            'label' => __('Force <a href="https://en.wikipedia.org/wiki/HTTPS"><code>HTTPS</code></a> on all subdomains (be careful).', $this->id()),
					            ]);
				            }

				            if ($this->getPlugin()->usingWWW()) {
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
					            'description' => __('Rewrite feed queries to FeedBurner with a <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/307"><code>HTTP 307</code></a> temporary redirection.', $this->id()),
				            ]);
			            })
			            ->onFinalization(function () {
				            $this->getPlugin()->getApache()->flush();
			            });
		        })
		        ->addTab('wordpress', function (Tab $tab) {
			        if (!\defined('DOING_AJAX')) {
				        $this->getPlugin()->getWordPress()->flush();
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
						            Arr::set($values, "wordpress.base.$key", $this->getPlugin()
						                                                          ->getDefaultOption($key));
					            }
				            }

				            return $values;
			            });
		        });
	}

}