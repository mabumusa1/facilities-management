<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\MultitenancyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    MultitenancyServiceProvider::class,
];
