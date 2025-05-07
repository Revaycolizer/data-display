<?php

namespace App\Types;

enum BootStrap: string
{
    case V3 = '3';
    case V5 = '5';

    public function cdnCssUrl(): string
    {
        return match ($this) {
            self::V3 => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css',
            self::V5 => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
        };
    }

    public function cdnJsUrl(): string
    {
        return match ($this) {
            self::V3 => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js',
            self::V5 => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
        };
    }
}
