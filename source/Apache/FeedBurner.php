<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class FeedBurner
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class FeedBurner extends Config
{

	/**
	 * @inheritdoc
	 */
	public function getConfig(): string
	{
		$slug = $this->plugin->getOption('apache.feedburner');

		return <<<EOT

# ----------------------------------------------------------------------
# FeedBurner redirect
# ----------------------------------------------------------------------
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /(feed|wp-atom|wp-feed|wp-rss|wp-rdf|wp-commentsrss)(.+)\ HTTP/ [NC,OR]
    RewriteCond %{QUERY_STRING} ^feed [NC]
    RewriteCond %{HTTP_USER_AGENT} !^(FeedBurner|FeedValidator) [NC,OR]
	RewriteCond %{REMOTE_HOST} ^ping.feedburner.com [NC,OR]
	RewriteCond %{REMOTE_HOST} ^feedburner.google.com [NC,OR]
	RewriteCond %{REMOTE_HOST} ^feedburner.com [NC]
    RewriteRule .* http://feeds.feedburner.com/{$slug}? [L,R=307]
</IfModule>

EOT;

	}

}