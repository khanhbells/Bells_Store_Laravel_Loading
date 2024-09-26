<?php
return [
    'module' => [
        [
            'title' => '会员管理',
            'icon' => 'fa fa-user-circle-o',
            'name' => ['user'],
            'subModule' => [
                [
                    'title' => '会员组',
                    'route' => 'user.catalogue.index' // 只使用路由名称
                ],
                [
                    'title' => '会员',
                    'route' => 'user.index' // 只使用路由名称
                ],
            ]
        ],
        [
            'title' => '文章管理',
            'icon' => 'fa fa-pencil-square-o',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => '文章分类',
                    'route' => 'post.catalogue.index' // 只使用路由名称
                ],
                [
                    'title' => '文章',
                    'route' => 'post.index' // 只使用路由名称
                ],
            ]
        ],
        [
            'title' => '通用设置',
            'icon' => 'fa fa-cog',
            'name' => ['language'],
            'subModule' => [
                [
                    'title' => '语言',
                    'route' => 'language.index' // 只使用路由名称
                ],
            ]
        ]
    ],
];
