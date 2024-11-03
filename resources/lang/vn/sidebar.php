<?php
return [
    'module' => [
        [
            'title' => 'Quản lý quản trị viên',
            'icon' => 'fa fa-user-circle-o',
            'name' => ['user', 'permission'],
            'subModule' => [
                [
                    'title' => 'Nhóm quản trị viên',
                    'route' => 'user.catalogue.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Thành viên quản trị',
                    'route' => 'user.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Phân quyền',
                    'route' => 'permission.index' // chỉ dùng tên route
                ],
            ]
        ],
        [
            'title' => 'Quản lý khách hàng',
            'icon' => 'fa fa-user-plus',
            'name' => ['customer'],
            'subModule' => [
                [
                    'title' => 'Nhóm khách hàng',
                    'route' => 'customer.catalogue.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Khách hàng',
                    'route' => 'customer.index' // chỉ dùng tên route
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
        [
            'title' => 'Quản lý marketing',
            'icon' => 'fa fa-credit-card',
            'name' => ['promotion', 'source'],
            'subModule' => [
                [
                    'title' => 'Khuyến mại',
                    'route' => 'promotion.index' // chỉ dùng tên route
                ],
                // [
                //     'title' => 'Nguồn khách',
                //     'route' => 'source.index' // chỉ dùng tên route
                // ],
            ]
        ],
        [
            'title' => 'Quản lý đơn hàng',
            'icon' => 'fa fa-calendar-check-o',
            'name' => ['order'],
            'subModule' => [
                [
                    'title' => 'Đơn hàng',
                    'route' => 'order.index' // chỉ dùng tên route
                ],
                // [
                //     'title' => 'Nguồn khách',
                //     'route' => 'source.index' // chỉ dùng tên route
                // ],
            ]
        ],
        // promotion coupon
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
            'title' => 'Quản lý slide/banner',
            'icon' => 'fa fa-picture-o',
            'name' => ['slide'],
            'subModule' => [
                [
                    'title' => 'Cài đặt slide',
                    'route' => 'slide.index' // chỉ dùng tên route
                ],
            ]
        ],
        [
            'title' => 'Cài đặt chung',
            'icon' => 'fa fa-cog',
            'name' => ['language', 'generate', 'system', 'widget'],
            'subModule' => [
                [
                    'title' => 'Ngôn ngữ',
                    'route' => 'language.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Tự sáng tạo',
                    'route' => 'generate.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Cấu hình hệ thống',
                    'route' => 'system.index' // chỉ dùng tên route
                ],
                [
                    'title' => 'Cấu hình Widget',
                    'route' => 'widget.index' // chỉ dùng tên route
                ],
            ]
        ]
    ],
];
