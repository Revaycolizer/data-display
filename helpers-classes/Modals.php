<?php

namespace App\Helpers;

use App\Types\BootStrap;

class Modals
{
    public static function showViewModal(BootStrap $bootStrap, string $viewModalId)
    {
        switch ($bootStrap) {

            case BootStrap::V5:
                echo '
            var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("' .
                    $viewModalId .
                    '"));
            modal.show();
            });
            ';
                break;

            case BootStrap::V3:
                echo '
            $("#' . $viewModalId . '").modal("show");
            ';
                break;

        }

    }

    public static function showEditModal(BootStrap $bootStrap, string $editModalId)
    {
        switch ($bootStrap) {

            case BootStrap::V5:

                echo '
            var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("' .
                    $editModalId .
                    '"));
            modal.show();
        })';
                break;

                case BootStrap::V3:
                    echo '
            $("#' . $editModalId . '").modal("show");
            ';
                    break;
        }
    }
}