<?php namespace Config;

use CodeIgniter\Config\AutoloadConfig;

class Autoload extends AutoloadConfig
{
    /**
     * --------------------------------------------------------------------------
     * Namespaces
     * --------------------------------------------------------------------------
     * This maps the location of the App namespace to its directory.
     * The 'Config' and 'CodeIgniter' namespaces are already set by the framework.
     *
     * @var array<string, list<string>|string>
     */
    public $psr4 = [
        APP_NAMESPACE => APPPATH,
    ];

    /**
     * --------------------------------------------------------------------------
     * Class Map
     * --------------------------------------------------------------------------
     * The class map provides a map of class names and their exact location.
     *
     * @var array<class-string, string>
     */
    public $classmap = [];

    /**
     * --------------------------------------------------------------------------
     * Files to Autoload
     * --------------------------------------------------------------------------
     * List of non-class files to autoload (e.g., helper functions).
     *
     * @var list<string>
     */
    public $files = [
        APPPATH . 'Helpers/auth_helper.php',
    ];

    /**
     * --------------------------------------------------------------------------
     * Helpers
     * --------------------------------------------------------------------------
     * List of helper files to autoload (without the '_helper' suffix).
     *
     * @var list<string>
     */
    public $helpers = [];

    /**
     * --------------------------------------------------------------------------
     * Namespaces to Ignore
     * --------------------------------------------------------------------------
     * @var list<string>
     */
    public $ignore = [];
}
