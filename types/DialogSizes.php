<?php

namespace App\Types;
enum DialogSizes: string
{
    case DEFAULT = '';
    case SMALL = 'modal-sm';
    case LARGE = 'modal-lg';
    case XLARGE = 'modal-xl';
    case FULLSCREEN = 'modal-fullscreen';
}
