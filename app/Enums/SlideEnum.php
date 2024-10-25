<?php

namespace App\Enums;

class SlideEnum
{
    const BANNER = 'banner';
    const BANNER_BODY = 'banner-body';

    public static function toArray()
    {
        return [
            self::BANNER => 'banner',
            self::BANNER_BODY => 'banner-body'
        ];
    }
}
