<?php
namespace App\Types;

enum DataSourceType: string
{
    case DOCTRINE = 'doctrine';
    case CLASSES = 'classes';
}
