<?php

namespace App\Helpers;

class DataTableScript
{
    public static function render(string $tableId, bool $delayDataTable, mixed $generateDataTableButtons)
    {
        return '
        var tableId = "#' . $tableId . '";
        
        ' . ($delayDataTable ? '
        setTimeout(function () {
            if ($(tableId).length) {
                $(tableId).DataTable({
                    dom: "Bfrtip",
                    buttons: ' . json_encode($generateDataTableButtons) . '
                });
            }
        }, 100);' : '
        if ($(tableId).length) {
            $(tableId).DataTable({
                dom: "Bfrtip",
                buttons: ' . json_encode($generateDataTableButtons) . '
            });
        }') . '
    ';
    }

}