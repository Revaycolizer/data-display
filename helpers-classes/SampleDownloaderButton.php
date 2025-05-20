<?php

namespace App\Helpers;

use App\Types\BootStrap;

class SampleDownloaderButton
{
    public static function show(BootStrap $bootstrap,$buttonLabel,$customImportAction,$importAction,$columns = [], $template = 'template.xlsx')
    {
        $token = TokenGenerator::generate();
        $label = $buttonLabel ?? 'Download Template';

        $tokenFieldName = $customImportAction ? 'csrf_token' : 'token';
        $actionFieldName = $customImportAction ? 'form_action' : 'action';
        $actionValue = $customImportAction ? 'import' : htmlspecialchars($importAction);
        $formAction = $customImportAction ? htmlspecialchars($customImportAction) : '';
        switch ($bootstrap) {

            case BootStrap::V5:
                echo '
            
            <form method="post"  action="' . $formAction . '" enctype="multipart/form-data" class="d-flex align-items-center flex-wrap gap-2">
                <input type="hidden" name="' . $tokenFieldName . '" value="' . $token . '" />
                <input type="hidden" name="' . $actionFieldName.'" value="' . $actionValue . '" />
  <div class="row">
    <div class="col-md-8">
        <input type="file" name="attachment" class="select-file form-control">
    </div>
    <div class="col-md-4">

        <button class="btn btn-success" type="submit"><i class="fa fa-download"></i>&nbsp;<b>Import</b></button>
    </div>
      <a href="javascript:;" onclick="downloadTemplate()">' . $label . '</a>
</div>
            </form>
             
            ';

                self::downloadTemplate($columns, $template);

                break;

            case BootStrap::V3:
                echo '
<form method="post" action="' . $formAction . '"  enctype="multipart/form-data" class="form-horizontal">
     <input type="hidden" name="' . $tokenFieldName . '" value="' . $token . '" />
                <input type="hidden" name="' . $actionFieldName.'" value="' . $actionValue . '" />

    <div class="form-group">
        <div class="row">
            <div class="col-md-8">
                <input type="file" name="attachment" class="form-control">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-download"></i>&nbsp;<b>Import</b>
                </button>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            <a href="javascript:;" onclick="downloadTemplate()">' . $label . '</a>
        </div>
    </div>
</form>';


                self::downloadTemplate($columns, $template);
                break;

        }
    }

    public static function downloadTemplate(array $columns = [], string $filename = 'template.xlsx')
    {
        if (empty($columns)) {
            $columns = [
                ['name' => 'Name', 'type' => 'string', 'required' => true],
                ['name' => 'Email', 'type' => 'string', 'required' => true],
                ['name' => 'Age', 'type' => 'integer', 'required' => false],
            ];
        }

        $columnsJson = json_encode($columns);
        $filenameJs = json_encode($filename);

        echo '
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function downloadTemplate() {
   let columns = ' . $columnsJson . ';
   let filename = ' . $filenameJs . ';

    const headers = columns.map(col => col.name);
    const ws_data = [headers];
    const worksheet = XLSX.utils.aoa_to_sheet(ws_data);

   
    if (!worksheet["!dataValidations"]) {
        worksheet["!dataValidations"] = {};
    }

    columns.forEach((col, idx) => {
        const colLetter = XLSX.utils.encode_col(idx);
        const range = colLetter + "2:" + colLetter + "1000"; 

        let validation = null;

        if (col.type === "integer" || col.type === "number") {
            validation = {
                type: "whole",
                operator: "between",
                allowBlank: !col.required,
                formula1: 0,
                formula2: 9999999,
                showInputMessage: true,
                promptTitle: "Number required",
                prompt: "Enter a valid integer",
                showErrorMessage: true,
                errorTitle: "Invalid input",
                error: "This field requires an integer"
            };
        } else if (col.type === "date") {
            validation = {
                type: "date",
                operator: "greaterThan",
                allowBlank: !col.required,
                formula1: "1900-01-01",
                showInputMessage: true,
                promptTitle: "Date required",
                prompt: "Enter a valid date",
                showErrorMessage: true,
                errorTitle: "Invalid date",
                error: "Please enter a valid date"
            };
        }

        if (validation) {
            if (!worksheet["!dataValidations"][range]) {
                worksheet["!dataValidations"][range] = validation;
            }
        }
    });

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Template");

    XLSX.writeFile(workbook, filename);
}
</script>
    ';
    }


}