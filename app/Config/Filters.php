<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Aliases
     * --------------------------------------------------------------------------
     *
     * This array lists all of the filter aliases that are available in
     * your application. It works similarly to the $aliases array in
     * the Autoload config file.
     *
     * @var array
     */
    public $aliases = [
        'csrf'          => \CodeIgniter\Filters\CSRF::class,
        'toolbar'       => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot'      => \CodeIgniter\Filters\Honeypot::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'rbac'          => \App\Filters\RBACFilter::class,
        'role'          => \App\Filters\RBACFilter::class,
        'permission'    => \App\Filters\RBACFilter::class,
    ];

    /**
     * --------------------------------------------------------------------------
     * Filters
     * --------------------------------------------------------------------------
     *
     * The filter array specifies what filters to apply to which URIs. The
     * key should be the filter name, and the array should contain the
     * URI patterns that the filter should be applied to.
     *
     * The filter name can be a class name or an alias.
     *
     * @var array
     */
    public $globals = [
        'before' => [
            // 'honeypot',
            'csrf' => [
                'except' => [
                    '/login',
                    '/register',
                    '/forgot-password',
                    '/reset-password/*',
                    '/api/*'
                ]
            ],
        ],
        'after' => [
            'toolbar',
            'honeypot',
        ],
    ];

    public $filters = [];
}
