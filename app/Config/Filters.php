<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes.
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'role'          => \App\Filters\RoleFilter::class,
        'permission'    => \App\Filters\PermissionFilter::class,
        'shift'         => \App\Filters\ShiftFilter::class,
        'api-auth'      => \App\Filters\ApiFilter::class,
    ];

    /**
     * List of special required filters.
     */
    public array $required = [
        'before' => [
            'forcehttps', 
            'pagecache',  
        ],
        'after' => [
            'pagecache',   
            'performance', 
            'toolbar',     
        ],
    ];

    /**
     * List of filter aliases applied globally.
     */
    public array $globals = [
    'before' => [
        // 'auth' => [...] <-- HAPUS ATAU KOMENTARKAN BARIS INI
        'cors',
        'csrf' => ['except' => ['api/*', 'auth/login']],
        'forcehttps',
        'pagecache',
    ],
    'after' => [
        'toolbar',
    ],
];

    public array $methods = [];

    public array $filters = [];
}