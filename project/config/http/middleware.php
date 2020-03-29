<?php
use Opulence\Framework\Http\Middleware\CheckMaintenanceMode;
use CovidTrack\Application\Http\Middleware\CheckCsrfToken;
use CovidTrack\Application\Http\Middleware\Session;

/**
 * ----------------------------------------------------------
 * Define the list of middleware to be run on all routes
 * ----------------------------------------------------------
 */
return [
    CheckMaintenanceMode::class,
    Session::class,
    CheckCsrfToken::class
];
