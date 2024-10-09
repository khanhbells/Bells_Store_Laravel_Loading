<?php
return [
    'module' => [
        [
            'title' => 'Member Management',
            'icon' => 'fa fa-user-circle-o',
            'name' => ['user', 'permission'],
            'subModule' => [
                [
                    'title' => 'Member Group',
                    'route' => 'user.catalogue.index' // use route name only
                ],
                [
                    'title' => 'Members',
                    'route' => 'user.index' // use route name only
                ],
                [
                    'title' => 'Permissions',
                    'route' => 'permission.index' // use route name only
                ],
            ]
        ],
        [
            'title' => 'Product Management',
            'icon' => 'fa fa-cube',
            'name' => ['product', 'attribute'],
            'subModule' => [
                [
                    'title' => 'Product Group',
                    'route' => 'product.catalogue.index' // use route name only
                ],
                [
                    'title' => 'Products',
                    'route' => 'product.index' // use route name only
                ],
                [
                    'title' => 'Attribute Type',
                    'route' => 'attribute.catalogue.index' // use route name only
                ],
                [
                    'title' => 'Attributes',
                    'route' => 'attribute.index' // use route name only
                ],
            ]
        ],
        // [
        //     'title' => 'Image Management',
        //     'icon' => 'fa fa-picture-o',
        //     'name' => ['gallery'],
        //     'subModule' => [
        //         [
        //             'title' => 'Image Group',
        //             'route' => 'gallery.catalogue.index' // use route name only
        //         ],
        //         [
        //             'title' => 'Images',
        //             'route' => 'user.index' // use route name only
        //         ],
        //     ]
        // ],
        [
            'title' => 'Post Management',
            'icon' => 'fa fa-pencil-square-o',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'Post Group',
                    'route' => 'post.catalogue.index' // use route name only
                ],
                [
                    'title' => 'Posts',
                    'route' => 'post.index' // use route name only
                ],

            ]
        ],
        [
            'title' => 'General Settings',
            'icon' => 'fa fa-cog',
            'name' => ['language', 'generate', 'system'],
            'subModule' => [
                [
                    'title' => 'Languages',
                    'route' => 'language.index' // use route name only
                ],
                [
                    'title' => 'Functions',
                    'route' => 'generate.index' // use route name only
                ],
                [
                    'title' => 'System Configuration',
                    'route' => 'system.index' // use route name only
                ],
            ]
        ]
    ],
];
