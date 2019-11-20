<?php

/**
 * @package     Dipity
 * @copyright   Copyright (C) 2014 - 2017 Dipity B.V. All rights reserved.
 * @link        http://www.dipity.eu
 */

return [
    /* Cache */
    'caching' => 1,
    'cachetime' => 1800, // In seconds

    /* Debug */
    'debug' => 0,
    'translation_debug' => 0,

    /* Maintanance */
    'offline' => 0,

    /* Slim settings */
    'determineRouteBeforeAppMiddleware' => true,

    /* ID separator */
    'id_separator' => '-fid',

    /* Project settings */
    'project' => [
        'path' => ROOT_PATH . '/project'
    ],

    /* Locale settings */
    'locales' => [
        'filter' => ['en-GB']
    ],

    /* Template (Twig) settings */
    'template' => [
        'path' => ROOT_PATH . '/project/templates',
        'cache' => [
            'path' => ROOT_PATH . '/cache/twig'
        ],
        'debug' => true
    ],
    'fem_host' => getenv('FEM_HOST') ?: 'http://manager.getfrontender.com'
];
