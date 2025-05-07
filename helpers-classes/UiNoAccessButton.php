<?php

namespace App\Helpers;
use App\Types\BootStrap;

class UiNoAccessButton
{
    public static function show(BootStrap $bootStrap, ?string $buttonLabel = null, ?string $extraBtnClass = null)
    {
        switch ($bootStrap) {

            case BootStrap::V5:
                echo "<button class='btn btn-sm btn-secondary disabled $extraBtnClass' data-bs-toggle='tooltip' title='You do not have access for this feature' disabled> 
            <span class='fa fa-info-circle'></span> $buttonLabel
          </button>";
                break;

            case BootStrap::V3:
                echo "<button class='btn btn-sm btn-default btn-disabled $extraBtnClass' data-toggle='tooltip' title='You do not have access for this feature'> <span class='fa fa-info-circle'></span> $buttonLabel</button>";
                break;

        }

    }

}