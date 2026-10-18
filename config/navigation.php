<?php

return [
    'sidebar_links' => [
        [
            'label' => 'Dashboard',
            'icon' => 'home',
            'route' => 'dashboard',
            'active' => 'dashboard',
        ],
        [
            'label' => 'Users',
            'icon' => 'users',
            'route' => 'users.index',
            'active' => 'users.*',
        ],
        [
            'label' => 'Products',
            'icon' => 'device-phone-mobile',
            'route' => 'products.index',
            'active' => 'products.*',
        ],
        [
            'label' => 'Categories',
            'icon' => 'tag',
            'route' => 'categories.index',
            'active' => 'categories.*',
        ],
        [
            'label' => 'Surveys',
            'icon' => 'clipboard',
            'route' => 'surveys.index',
            'active' => 'surveys.*',
        ],
        [
            'label' => 'Customers',
            'icon' => 'user',
            'route' => 'customers.index',
            'active' => 'customers.*',
        ],
        [
            'label' => 'Activity Log',
            'icon' => 'clock',
            'route' => 'activity-log.index',
            'active' => 'activity-log.*',
            'can' => 'view-activity-log',
        ],
    ],
];
