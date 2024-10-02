<?php
return [
    'module' => [
        [
            'title' => 'Quản lý thành viên',
            'icon' => 'fa fa-user-circle-o',
            'name' => ['user', 'permission'],
            'subModule' => [
                [
                    'title' => 'Nhóm thành viên',
                    'route' => 'user.catalogue.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Thành viên',
                    'route' => 'user.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Phân quyền',
                    'route' => 'permission.index' // chỉ dùng tên route
                ],
            ]
        ],
        [
            'title' => 'Quản lý bài viết',
            'icon' => 'fa fa-pencil-square-o',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'Nhóm bài viết',
                    'route' => 'post.catalogue.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Bài viết',
                    'route' => 'post.index' // chỉ dùng tên route
                ],

            ]
        ],
        [
            'title' => 'Cấu hình chung',
            'icon' => 'fa fa-cog',
            'name' => ['language', 'generate'],
            'subModule' => [
                [
                    'title' => 'Ngôn ngữ',
                    'route' => 'language.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Module',
                    'route' => 'generate.index' // chỉ dùng tên route
                ],
            ]
        ]
    ],
];
