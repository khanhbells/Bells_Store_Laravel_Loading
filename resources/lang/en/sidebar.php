<?php
return [
    'module' => [
        [
            'title' => 'Member Management',
            'icon' => 'fa fa-user-circle-o',
            'name' => ['user'],
            'subModule' => [
                [
                    'title' => 'Member Group',
                    'route' => 'user.catalogue.index' // only use route name
                ],
                [
                    'title' => 'Member',
                    'route' => 'user.index' // only use route name
                ],
                [
                    'title' => 'Permission',
                    'route' => 'permission.index' // chỉ dùng tên route
                ],
            ]
        ],
        [
            'title' => 'Post Management',
            'icon' => 'fa fa-pencil-square-o',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'Post Group',
                    'route' => 'post.catalogue.index' // only use route name
                ],
                [
                    'title' => 'Post',
                    'route' => 'post.index' // use only route name
                ],
            ]
        ],
        [
            'title' => 'General Configuration',
            'icon' => 'fa fa-cog',
            'name' => ['language'],
            'subModule' => [
                [
                    'title' => 'Language',
                    'route' => 'language.index' // use only route name
                ],
            ]
        ]
    ],
];
