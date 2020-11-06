<?php

return array(
    'dsn' => env('SENTRY_BACKEND_DSN'),

    // capture release as git sha
    'release' => trim(exec('git rev-parse --abbrev-ref HEAD')),

    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,

    // Capture default user context
    'user_context' => false,

    'frontend' => [
        'dns' => env('SENTRY_FRONTEND_DSN'),
    ]
);
