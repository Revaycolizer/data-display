<?php

namespace App\Helpers;

use App\Types\BootStrap;

class Searchable
{
    public static function show(BootStrap $bootstrap, array $searchableColumns)
    {
        switch ($bootstrap){
            case BootStrap::V5:
                if (!empty($searchableColumns)) {
                    echo '<form method="POST" class="row g-3 mb-3">';

                    foreach ($searchableColumns as $column => $config) {
                        $label = $config["label"] ?? ucfirst($column);
                        $value = htmlspecialchars($_POST[$column] ?? "");

                        if ($config["type"] === "input") {
                            echo "<div class='col-auto'>
                              <label for='$column' class='form-label'>$label</label>
                              <input type='text' class='form-control form-control-sm' name='$column' id='$column' value='" .
                                htmlspecialchars($value) .
                                "'>
                          </div>";
                        } elseif ($config["type"] === "select") {
                            echo "<div class='col-auto'>
                              <label for='$column' class='form-label'>$label</label>
                              <select name='$column' id='$column' class='form-control form-control-sm'>
                                  <option value=''>-- Select $label --</option>";
                            foreach ($config["options"] as $opt) {
                                $val = $opt[$config["value_field"]];
                                $text = $opt[$config["label_field"]];
                                $selected = $value == $val ? "selected" : "";
                                echo "<option value='$val' $selected>$text</option>";
                            }
                            echo "</select></div>";
                        }
                    }

                    echo '<div class="col-auto align-self-end">';
                    echo '<button type="submit" class="btn btn-primary">Search</button>';
                    echo "</div>";

                    echo "</form>";
                }
                break;

            case BootStrap::V3:
                if (!empty($searchableColumns)) {
                    echo '<form method="POST" class="form-inline" role="form" style="margin-bottom: 15px;">';

                    foreach ($searchableColumns as $column => $config) {
                        $label = $config["label"] ?? ucfirst($column);
                        $value = htmlspecialchars($_POST[$column] ?? "");

                        echo "<div class='form-group' style='margin-right: 10px;'>";
                        echo "<label for='$column' class='control-label'>$label</label> ";

                        if ($config["type"] === "input") {
                            echo "<input type='text' class='form-control' name='$column' id='$column' value='" .
                                htmlspecialchars($value) . "'>";
                        } elseif ($config["type"] === "select") {
                            echo "<select name='$column' id='$column' class='form-control'>
                    <option value=''>-- Select $label--</option>";
                            foreach ($config["options"] as $opt) {
                                $val = $opt[$config["value_field"]];
                                $text = $opt[$config["label_field"]];
                                $selected = $value == $val ? "selected" : "";
                                echo "<option value='$val' $selected>$text</option>";
                            }
                            echo "</select>";
                        }

                        echo "</div>";
                    }

                    echo '<div class="form-group">';
                    echo '<button type="submit" class="btn btn-primary">Search</button>';
                    echo '</div>';

                    echo '</form>';
                }
              break;
        }
}
}