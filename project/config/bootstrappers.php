<?php
use Opulence\Framework\Cryptography\Bootstrappers\CryptographyBootstrapper;
use CovidTrack\Application\Bootstrappers\Cache\RedisBootstrapper;
use CovidTrack\Application\Bootstrappers\Databases\SqlBootstrapper;
use CovidTrack\Application\Bootstrappers\Events\EventDispatcherBootstrapper;
use CovidTrack\Application\Bootstrappers\Orm\OrmBootstrapper;
use CovidTrack\Application\Bootstrappers\Validation\ValidatorBootstrapper;

/**
 * ----------------------------------------------------------
 * Define the bootstrapper classes for all applications
 * ----------------------------------------------------------
 */
return [
    CryptographyBootstrapper::class,
    EventDispatcherBootstrapper::class,
    SqlBootstrapper::class,
    RedisBootstrapper::class,
    OrmBootstrapper::class,
    ValidatorBootstrapper::class
];
