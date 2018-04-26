<?php

namespace ic\Plugin\RewriteControl;

use ic\Plugin\RewriteControl\Apache\Base;
use ic\Plugin\RewriteControl\Apache\Charset;
use ic\Plugin\RewriteControl\Apache\CORS;
use ic\Plugin\RewriteControl\Apache\Deflate;
use ic\Plugin\RewriteControl\Apache\Root;
use ic\Plugin\RewriteControl\Apache\Expires;
use ic\Plugin\RewriteControl\Apache\IE;
use ic\Plugin\RewriteControl\Apache\FeedBurner;
use ic\Plugin\RewriteControl\Apache\MIME;
use ic\Plugin\RewriteControl\Apache\Protect;
use ic\Plugin\RewriteControl\Apache\Search;
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
    protected static $config = [
        'protect'    => Protect::class,
        'cors'       => CORS::class,
        'ie'         => IE::class,
        'mime'       => MIME::class,
        'charset'    => Charset::class,
        'deflate'    => Deflate::class,
        'expires'    => Expires::class,
        'root'       => Root::class,
        'feedburner' => FeedBurner::class,
        'ssl'        => SSL::class,
        'www'        => WWW::class,
        'search'     => Search::class,
        'base'       => Base::class,
    ];

    /**
     * @var RewriteControl
     */
    protected $plugin;

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
     *
     */
    public function flush(): void
    {
        save_mod_rewrite_rules();
    }

}