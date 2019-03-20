<?php

namespace ic\Plugin\RewriteControl\Apache;

use ic\Plugin\RewriteControl\RewriteControl;

/**
 * Class WordPress
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class WordPress extends ApacheConfig
{

	/**
	 * @var string
	 */
	protected $directives;

	/**
	 * @inheritdoc
	 */
	public function __construct(RewriteControl $plugin, string $directives)
	{
		parent::__construct($plugin);

		$this->directives = $directives;
	}

	/**
	 * @inheritdoc
	 */
	public function isEnabled(): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$directives = preg_replace('/^Rewrite/m', '    Rewrite', $this->directives);
		return <<<EOT

# ----------------------------------------------------------------------
# WordPress
# ----------------------------------------------------------------------
$directives
EOT;
	}

}