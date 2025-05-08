<?php

namespace App\Helpers;

use App\Types\BootStrap;

class AddButton
{
    public static function show(BootStrap $bootstrap, $buttonLabel, $modalId)
    {
        switch ($bootstrap) {

            case BootStrap::V5:
                echo '<button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#' .
                    $modalId .
                    '">' .
                    htmlspecialchars($buttonLabel) .
                    "</button>";
                break;

            case BootStrap::V3:
                echo '<button type="button" class="btn btn-success" style="margin-bottom: 5px;" data-toggle="modal" data-target="#' .
                    $modalId .
                    '">' .
                    htmlspecialchars($buttonLabel) .
                    "</button>";
                break;

            default:
                echo '<button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#' .
                    $modalId .
                    '">' .
                    htmlspecialchars($buttonLabel) .
                    "</button>";
                break;

        }

    }
}