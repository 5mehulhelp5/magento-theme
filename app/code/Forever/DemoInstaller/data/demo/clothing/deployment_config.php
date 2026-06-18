<?php
/**
 * Static configuration for the Clothing demo.
 *
 * This is the SAME shape as the "system" => "default" block inside app/etc/env.php.
 * When the Clothing demo is imported, the DeploymentConfigImporter writes this whole
 * "system" tree into app/etc/config.php, so the values become static / locked in admin
 * (greyed-out, "the value is stored in a deployment config file").
 *
 * Only put NON-SECRET, shareable, theme/storefront configuration here.
 * Never put db / crypt / session / payment credentials in a demo package.
 */
return [
    'system' => [
        'default' => [
            // --- Forever theme options (Stores > Configuration > Forever) ---
            'forever_general' => [
                'header' => [
                    'style'        => 'style1',
                    'sticky'       => '1',
                    'stickyheader' => 'sticky1',
                ],
                'footer' => [
                    'style' => 'style1',
                ],
            ],

            // --- Storefront basics ---
            'web' => [
                'default' => [
                    'front' => 'cms',
                ],
                'seo' => [
                    'use_rewrites' => '1',
                ],
            ],

            // --- Design / theme head ---
            'design' => [
                'header' => [
                    'welcome' => 'Welcome to the Clothing Demo Store',
                ],
                'footer' => [
                    'copyright' => 'Copyright (c) Forever Clothing Demo. All rights reserved.',
                ],
            ],

            // --- Catalog storefront ---
            'catalog' => [
                'frontend' => [
                    'grid_per_page'        => '12',
                    'list_per_page'        => '10',
                    'default_sort_by'      => 'position',
                    'list_allow_all'       => '1',
                ],
            ],
        ],
    ],
];
