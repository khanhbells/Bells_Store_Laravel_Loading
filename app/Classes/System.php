<?php

namespace App\Classes;

class System
{

    public function config()
    {
        $data['homepage'] = [
            'label' => 'Thông tin chung',
            'description' => 'Cài đặt đầy đủ thông tin chung website. Tên thương hiệu website, Logo, Favicon, vv...',
            'value' => [
                'company' => ['type' => 'text', 'label' => 'Tên công ty'],
                'brand' => ['type' => 'text', 'label' => 'Tên thương hiệu'],
                'slogan' => ['type' => 'text', 'label' => 'Slogan'],
                'logon' => ['type' => 'images', 'label' => 'Logo website', 'title' => 'Click vào ô phía dưới để tải logo'],
                'favicon' => ['type' => 'images', 'label' => 'Favicon', 'title' => 'Click vào ô phía dưới để tải logo'],
                'copyright' => ['type' => 'text', 'label' => 'Copyright'],
                'website' => [
                    'type' => 'select',
                    'label' => 'Tình trạng website',
                    'option' => [
                        'open' => 'Mở cửa website',
                        'close' => 'Website đang bảo trì'
                    ]
                ],
                'short_intro' => [
                    'type' => 'editor',
                    'label' => 'Giới thiệu ngắn'
                ]
            ]
        ];
        $data['contact'] = [
            'label' => 'Thông tin liên hệ',
            'description' => 'Cài đặt đầy đủ thông tin liên hệ của website ví dụ: Địa chỉ công ty, Văn phòng giao dịch, Hotline, Bản đồ,vv',
            'value' => [
                'office' => ['type' => 'text', 'label' => 'Văn phòng giao dịch'],
                'address' => ['type' => 'text', 'label' => 'Địa chỉ'],
                'hotline' => ['type' => 'text', 'label' => 'Hotline'],
                'technical_phone' => ['type' => 'text', 'label' => 'Hotline kỹ thuật'],
                'sell_phone' => ['type' => 'text', 'label' => 'Hotline kinh doanh'],
                'phone' => ['type' => 'text', 'label' => 'Số cố định'],
                'fax' => ['type' => 'text', 'label' => 'Fax'],
                'email' => ['type' => 'text', 'label' => 'Email'],
                'tax' => ['type' => 'text', 'label' => 'Mã số thuế'],
                'website' => ['type' => 'text', 'label' => 'Website'],
                'map' => [
                    'type' => 'textarea',
                    'label' => 'Bản đồ',
                    'link' => [
                        'text' => 'Hướng dẫn thiết lập bản đồ',
                        'href' => 'https://www.facebook.com/vu.baokhanh.71',
                        'target' => '_blank'
                    ]
                ],

            ]
        ];
        $data['seo'] = [
            'label' => 'Cấu hình SEO dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về SEO của trang chủ website. Bao gồm tiêu đề SEO, Từ khóa SEO, Mô tả SEO, Meta images',
            'value' => [
                'meta_title' => ['type' => 'text', 'label' => 'Tiêu đề SEO'],
                'meta_keyword' => ['type' => 'text', 'label' => 'Từ khóa SEO'],
                'meta_description' => ['type' => 'editor', 'label' => 'Mô tả SEO'],
                'meta_images' => ['type' => 'images', 'label' => 'Ảnh SEO'],

            ]
        ];
        return $data;
    }
}
