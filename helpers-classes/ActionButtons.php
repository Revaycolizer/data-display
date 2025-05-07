<?php

namespace App\Helpers;

use App\Types\BootStrap;
use App\Types\ViewSource;

class ActionButtons
{
    public static function show(BootStrap $bootstrap,string $link,mixed $id,bool $canEdit,array $columnsToEdit,ViewSource $viewSource,array $columnsToRenderonModal, bool $canDelete,mixed $row,$buttonsViewable)
    {
        switch ($bootstrap) {

            case BootStrap::V5:

                echo "<td>";
                echo "<div class='btn btn-light dropdown hover-dropdown' role='button' data-bs-toggle='dropdown'>";
                echo "<a href='#' style='text-decoration: none !important;' class='ellipsis-trigger' aria-expanded='false'>";
                echo "&#8942;";
                echo "</a>";
                echo "<ul class='dropdown-menu'>";

                if(ButtonsViewable::edit($buttonsViewable)) {
                    if ($canEdit) {
                        echo "<li><a class='dropdown-item editBtn' href='#' data-id='{$id}'";
                        foreach ($columnsToEdit as $columnName => $config) {
                            $value = isset($row[$columnName]) && is_scalar($row[$columnName])
                                ? htmlspecialchars($row[$columnName])
                                : "";
                            echo " data-$columnName='$value'";
                        }
                        echo ">Edit</a></li>";
                    }
                }


                switch ($viewSource) {
                    case ViewSource::LINK:
                        if(ButtonsViewable::view($buttonsViewable)) {
                            echo "<li><a class='dropdown-item' href='{$link}' data-id='{$id}'>View</a></li>";
                        }
                        break;

                    case ViewSource::MODAL:
                        if(ButtonsViewable::view($buttonsViewable)) {
                            echo "<li><a class='dropdown-item viewBtn' href='#' data-id='{$row["id"]}'";

                            foreach ($columnsToRenderonModal as $columnName => $config) {
                                $value =
                                    isset($row[$columnName]) &&
                                    is_scalar($row[$columnName])
                                        ? htmlspecialchars($row[$columnName])
                                        : "";
                                echo " data-$columnName='$value'";
                            }
                            echo ">View</a></li>";
                        }
                        break;
                }

                if(ButtonsViewable::delete($buttonsViewable)) {
                    echo $canDelete ? "<li><a class='dropdown-item deleteBtn' href='#' data-id='{$id}'>Delete</a></li>" : "";
                }
                echo "</ul></div>";
                echo "</td>";

                break;

            case BootStrap::V3:
                echo "<td>";
                echo "<div class='btn btn-default dropdown-toggle' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
                echo "<a href='#' style='text-decoration: none !important;' class='ellipsis-trigger'>";
                echo "&#8942;";
                echo "</a>";
                echo "</div>";
                echo "<ul class='dropdown-menu'>";

                if(ButtonsViewable::edit($buttonsViewable)) {
                    if ($canEdit) {
                        echo "<li><a class='editBtn' href='#' data-id='{$id}'";
                        foreach ($columnsToEdit as $columnName => $config) {
                            $value = isset($row[$columnName]) && is_scalar($row[$columnName])
                                ? htmlspecialchars($row[$columnName])
                                : "";
                            echo " data-$columnName='$value'";
                        }
                        echo ">Edit</a></li>";
                    }
                }


                switch ($viewSource) {
                    case ViewSource::LINK:
                        if(ButtonsViewable::view($buttonsViewable)) {
                            echo "<li><a href='{$link}' data-id='{$id}'>View</a></li>";
                        }
                        break;

                    case ViewSource::MODAL:
                        if(ButtonsViewable::view($buttonsViewable)) {
                            echo "<li><a class='viewBtn' href='#' data-id='{$row["id"]}'";
                            foreach ($columnsToRenderonModal as $columnName => $config) {
                                $value = isset($row[$columnName]) && is_scalar($row[$columnName])
                                    ? htmlspecialchars($row[$columnName])
                                    : "";
                                echo " data-$columnName='$value'";
                            }
                            echo ">View</a></li>";
                        }
                        break;
                }

                if(ButtonsViewable::delete($buttonsViewable)) {
                    echo $canDelete ? "<li><a class='deleteBtn' href='#' data-id='{$id}'>Delete</a></li>" : "";
                }
                echo "</ul>";
                echo "</td>";
                break;

        }
}

    public static function default(BootStrap $bootstrap,string $link,mixed $id,bool $canEdit,array $columnsToEdit,ViewSource $viewSource,array $columnsToRenderonModal,bool $canDelete,mixed $row,array $buttonsViewable)
    {
        switch ($bootstrap) {

            case BootStrap::V5:
                echo "<td>";

                if(ButtonsViewable::edit($buttonsViewable)) {
                    if ($canEdit) {
                        echo "<button class='btn btn-primary btn-sm editBtn' data-id='{$id}'";
                        foreach ($columnsToEdit as $columnName => $config) {
                            $value =
                                isset($row[$columnName]) &&
                                is_scalar($row[$columnName])
                                    ? htmlspecialchars($row[$columnName])
                                    : "";
                            echo " data-$columnName='$value'";
                        }
                        echo ">Edit</button> ";
                    }
                }

                switch ($viewSource) {
                    case ViewSource::LINK:
                        if(ButtonsViewable::view($buttonsViewable)) {
                            echo "<a href='{$link}' class='btn btn-success btn-sm' target='_blank'>View</a>";
                        }
                        break;
                    case ViewSource::MODAL:
                        if(ButtonsViewable::view($buttonsViewable)) {
                            echo "<button class='btn btn-success btn-sm viewBtn' data-id='{$id}'";
                            foreach ($columnsToRenderonModal as $columnName => $config) {
                                $value =
                                    isset($row[$columnName]) &&
                                    is_scalar($row[$columnName])
                                        ? htmlspecialchars($row[$columnName])
                                        : "";
                                echo " data-$columnName='$value'";
                            }
                            echo ">View</button>";
                        }
                        break;
                }

                if(ButtonsViewable::delete($buttonsViewable)) {
                    echo $canDelete ? "
                      <button class='btn btn-danger btn-sm deleteBtn' data-id='{$id}'>Delete</button>" : "";
                }

                echo "</td>";
                break;

                case BootStrap::V3:
                    echo "<td>";
                    if(ButtonsViewable::edit($buttonsViewable)) {
                        if ($canEdit) {
                            echo "<button class='btn btn-primary btn-sm editBtn' data-id='{$id}'";
                            foreach ($columnsToEdit as $columnName => $config) {
                                $value =
                                    isset($row[$columnName]) && is_scalar($row[$columnName])
                                        ? htmlspecialchars($row[$columnName])
                                        : "";
                                echo " data-$columnName='$value'";
                            }
                            echo ">Edit</button> ";
                        }
                    }

                    switch ($viewSource) {
                        case ViewSource::LINK:
                            if(ButtonsViewable::view($buttonsViewable)) {
                                echo "<a href='{$link}' class='btn btn-success btn-sm' target='_blank'>View</a>";
                            }
                            break;
                        case ViewSource::MODAL:
                            if(ButtonsViewable::view($buttonsViewable)) {
                                echo "<button class='btn btn-success btn-sm viewBtn' data-id='{$id}'";
                                foreach ($columnsToRenderonModal as $columnName => $config) {
                                    $value =
                                        isset($row[$columnName]) && is_scalar($row[$columnName])
                                            ? htmlspecialchars($row[$columnName])
                                            : "";
                                    echo " data-$columnName='$value'";
                                }
                                echo ">View</button>";
                            }
                            break;
                    }

                    if(ButtonsViewable::delete($buttonsViewable)) {
                        echo $canDelete
                            ? "<button class='btn btn-danger btn-sm deleteBtn' data-id='{$id}'>Delete</button>"
                            : "";
                    }

                    echo "</td>";

                    break;

        }
    }
}