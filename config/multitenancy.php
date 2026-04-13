<?php

use App\Models\Tenant;
use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Tenant Header
    |--------------------------------------------------------------------------
    |
    | The HTTP header name used to identify the tenant in API requests.
    |
    */

    'header' => env('TENANT_HEADER', 'X-Tenant'),

    /*
    |--------------------------------------------------------------------------
    | Tenant Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of the Tenant model.
    |
    */

    'tenant_model' => Tenant::class,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of the User model.
    |
    */

    'user_model' => User::class,

    /*
    |--------------------------------------------------------------------------
    | Community Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of the Community model.
    | Used for scope-based access control.
    |
    */

    'community_model' => 'App\Models\Community',

    /*
    |--------------------------------------------------------------------------
    | Building Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of the Building model.
    | Used for scope-based access control.
    |
    */

    'building_model' => 'App\Models\Building',

    /*
    |--------------------------------------------------------------------------
    | Domain Configuration
    |--------------------------------------------------------------------------
    |
    | The base domain for subdomain-based tenant identification.
    |
    */

    'domain' => env('APP_DOMAIN', 'localhost'),

    /*
    |--------------------------------------------------------------------------
    | Require Tenant
    |--------------------------------------------------------------------------
    |
    | If true, requests without a valid tenant will be rejected.
    | If false, requests can proceed without tenant context.
    |
    */

    'require_tenant' => env('REQUIRE_TENANT', false),

    /*
    |--------------------------------------------------------------------------
    | Tenant Caching
    |--------------------------------------------------------------------------
    |
    | Configuration for tenant caching to improve performance.
    |
    */

    'cache' => [
        'enabled' => env('TENANT_CACHE_ENABLED', true),
        'ttl' => env('TENANT_CACHE_TTL', 3600), // 1 hour
        'prefix' => 'tenant_',
    ],

];
