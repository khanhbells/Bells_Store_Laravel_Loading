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
            'title' => 'Quản lý sản phẩm',
            'icon' => 'fa fa-cube',
            'name' => ['product', 'attribute'],
            'subModule' => [
                [
                    'title' => 'Nhóm Sản phẩm',
                    'route' => 'product.catalogue.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Sản phẩm',
                    'route' => 'product.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Loại thuộc tính',
                    'route' => 'attribute.catalogue.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Thuộc tính',
                    'route' => 'attribute.index' // chỉ dùng tên route
                ],
            ]
        ],
        // [
        //     'title' => 'Quản lý hình ảnh',
        //     'icon' => 'fa fa-picture-o',
        //     'name' => ['gallery'],
        //     'subModule' => [
        //         [
        //             'title' => 'Nhóm hình ảnh',
        //             'route' => 'gallery.catalogue.index' // chỉ dùng tên route
        //         ],
        //         [
        //             'title' => 'Hình ảnh',
        //             'route' => 'user.index' // chỉ dùng tên route
        //         ],
        //     ]
        // ],
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
            'title' => 'Quản lý menu',
            'icon' => 'fa fa-bars',
            'name' => ['menu'],
            'subModule' => [
                [
                    'title' => 'Cài đặt menu',
                    'route' => 'menu.index' // chỉ dùng tên route
                ],
            ]
        ],
        [
            'title' => 'Cấu hình chung',
            'icon' => 'fa fa-cog',
            'name' => ['language', 'generate', 'system'],
            'subModule' => [
                [
                    'title' => 'Ngôn ngữ',
                    'route' => 'language.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Chức năng',
                    'route' => 'generate.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Cấu hình hệ thống',
                    'route' => 'system.index' // chỉ dùng tên route
                ],
            ]
        ]
    ],
];
