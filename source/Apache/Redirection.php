<?php

namespace ic\Plugin\RewriteControl\Apache;

/**
 * Class Redirection
 *
 * @package ic\Plugin\RewriteControl\Apache
 */
class Redirection extends ApacheConfig
{

	/**
	 * @inheritdoc
	 */
	public static function initial()
	{
		return [
			'redirect'       => '',
			'redirect-match' => '',
		];
	}

	public function isEnabled(): bool
	{
		return !empty($this->getConfig('redirect')) || !empty($this->getConfig('redirect-match'));
	}

	/**
	 * @inheritdoc
	 */
	public function getDirectives(): string
	{
		$redirections = implode("\n", array_merge($this->getRedirections('redirect', 'Redirect'), $this->getRedirections('redirect-match', 'RedirectMatch')));

		return <<<EOT

# ----------------------------------------------------------------------
# Redirections
# ----------------------------------------------------------------------
$redirections

EOT;
	}

	/**
	 * @param string $config
	 * @param string $type
	 * @param int    $status
	 *
	 * @return array
	 */
	private function getRedirections(string $config, string $type, int $status = 301): array
	{
		$redirections = array_filter(explode("\n", $this->getConfig($config)));

		return array_map(static function ($redirect) use ($type, $status) {
			$parts = explode(' ', $redirect);
			$code  = (count($parts) === 3) ? trim(array_shift($parts)) : $status;
			[$from, $to] = array_map('trim', $parts);

			return "$type $code $from $to";
		}, $redirections);
	}

}