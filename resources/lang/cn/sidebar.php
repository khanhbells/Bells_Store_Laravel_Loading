<?php
return [
    'module' => [
        [
            'title' => '会员管理',
            'icon' => 'fa fa-user-circle-o',
            'name' => ['user', 'permission'],
            'subModule' => [
                [
                    'title' => '会员组',
                    'route' => 'user.catalogue.index' // 仅使用路由名称
                ],
                [
                    'title' => '会员',
                    'route' => 'user.index' // 仅使用路由名称
                ],
                [
                    'title' => '权限管理',
                    'route' => 'permission.index' // 仅使用路由名称
                ],
            ]
        ],
        [
            'title' => '产品管理',
            'icon' => 'fa fa-cube',
            'name' => ['product', 'attribute'],
            'subModule' => [
                [
                    'title' => '产品组',
                    'route' => 'product.catalogue.index' // 仅使用路由名称
                ],
                [
                    'title' => '产品',
                    'route' => 'product.index' // 仅使用路由名称
                ],
                [
                    'title' => '属性类型',
                    'route' => 'attribute.catalogue.index' // 仅使用路由名称
                ],
                [
                    'title' => '属性',
                    'route' => 'attribute.index' // 仅使用路由名称
                ],
            ]
        ],
        // [
        //     'title' => '图片管理',
        //     'icon' => 'fa fa-picture-o',
        //     'name' => ['gallery'],
        //     'subModule' => [
        //         [
        //             'title' => '图片组',
        //             'route' => 'gallery.catalogue.index' // 仅使用路由名称
        //         ],
        //         [
        //             'title' => '图片',
        //             'route' => 'user.index' // 仅使用路由名称
        //         ],
        //     ]
        // ],
        [
            'title' => '文章管理',
            'icon' => 'fa fa-pencil-square-o',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => '文章组',
                    'route' => 'post.catalogue.index' // 仅使用路由名称
                ],
                [
                    'title' => '文章',
                    'route' => 'post.index' // 仅使用路由名称
                ],

            ]
        ],
        [
            'title' => '通用配置',
            'icon' => 'fa fa-cog',
            'name' => ['language', 'generate', 'system'],
            'subModule' => [
                [
                    'title' => '语言',
                    'route' => 'language.index' // 仅使用路由名称
                ],
                [
                    'title' => '功能',
                    'route' => 'generate.index' // 仅使用路由名称
                ],
                [
                    'title' => '系统配置',
                    'route' => 'system.index' // 仅使用路由名称
                ],
            ]
        ]
    ],
];
