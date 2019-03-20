<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class RewriteFeedBurner
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class RewriteFeedBurner extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public static function initial()
	{
		return '';
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$slug = $this->getConfig();

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
    RewriteRule .* https://feeds.feedburner.com/{$slug}? [L,R=307]
</IfModule>

EOT;

	}

}