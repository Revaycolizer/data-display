<?php

namespace Revaycolizer\DataDisplay;

use App\Types\ActionsButtonMode;
use App\Types\DataSourceType;
use App\Types\ViewSource;
use InvalidArgumentException;

class DataDisplay
{
    private $entityName;
    private $entityManager;
    private $columnsToAdd = [];
    private $columnsToEdit = [];
    private $tablesToJoin = [];
    private $valuesToSelect = [];

    private $valuesToRender = [];
    private $token;
    private $paginationEnabled = false;
    private $recordsPerPage = 10;
    private $paginateColumns = [];
    private $addButtonLabel = "Add New Record";
    private $tableId = "dataTable";
    private $searchableColumns = [];
    private $dataTableButtons = [];
    private $canAdd = true;
    private $dataSource = "doctrine";

    private $editButtonConditions = [];

    private $editButtonConditionCallback = null;

    private $deleteButtonConditions = [];
    private $deleteButtonConditionCallback = null;

    private $customAddFormRenderer = null;
    private $customEditFormRenderer = null;

    private $customViewFormRenderer = null;
    private $customAddAction = null;
    private $customEditAction = null;

    private $customAddFormHeader = "Add New Record";
    private $customEditFormHeader = "Edit Record";

    private $customViewFormHeader = "View Record";
    private $rowDataTransformer = null;

    private $columnsBeforeActions = [];
    private $columnsAfterActions = [];
    private $classFetchDataFunction = "all";
    private $mode = "default";
    private $addDialogSize;
    private $editDialogSize;

    private $viewDialogSize;

    private $actionsButtonMode = ActionsButtonMode::DEFAULT;

    private $viewSource = ViewSource::LINK;

    private $viewLink;

    private $valuesToShowonModal = [];

    /**
     * @param 'doctrine'|'classes' $dataSource
     */
    public function __construct(
        object         $entityManager = null,
        string         $entityName,
        DataSourceType $dataSource = DataSourceType::DOCTRINE
    )
    {
        $this->dataSource = $dataSource;

        if ($dataSource === DataSourceType::DOCTRINE) {
            if (
                !interface_exists(\Doctrine\ORM\EntityManagerInterface::class)
            ) {
                throw new \RuntimeException(
                    "Doctrine is not installed but selected as data source."
                );
            }

            if (
                !$entityManager instanceof \Doctrine\ORM\EntityManagerInterface
            ) {
                throw new \InvalidArgumentException(
                    "Expected Doctrine EntityManager for doctrine data source."
                );
            }

            if (!$entityName) {
                throw new \InvalidArgumentException(
                    "Entity name must be provided for doctrine data source."
                );
            }
        }
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;
        $this->token = $this->generateCsrfToken();
    }

    public function setActionsButtonMode(ActionsButtonMode $mode = ActionsButtonMode::DEFAULT): self
    {
        $this->actionsButtonMode = $mode;
        return $this;
    }

    public function setViewLink($link): self
    {
        $this->viewLink = $link;
        return $this;
    }

    public function setValuesToShowonModal(array $values): self
    {
        $this->valuesToShowonModal = $values;
        return $this;
    }

    public function setViewSource(ViewSource $source = ViewSource::LINK): self
    {
        $this->viewSource = $source;
        return $this;
    }


    public function searchable(array $columns)
    {
        $this->searchableColumns = $columns;
        return $this;
    }

    public function setAddDialogSize(string $addDialogSize)
    {
        $this->addDialogSize = $addDialogSize;
        return $this;
    }

    public function setEditDialogSize(string $editDialogSize)
    {
        $this->editDialogSize = $editDialogSize;
        return $this;
    }

    public function setViewDialogSize(string $viewDialogSize)
    {
        $this->viewDialogSize = $viewDialogSize;
        return $this;
    }

    public static function create(
        object         $entityManager = null,
        string         $entityName = null,
        DataSourceType $dataSource = DataSourceType::DOCTRINE
    ): self
    {
        if ($dataSource === DataSourceType::DOCTRINE) {
            if (
                !interface_exists(\Doctrine\ORM\EntityManagerInterface::class)
            ) {
                throw new \RuntimeException("Doctrine is not installed.");
            }

            if (!$entityName || !class_exists($entityName)) {
                throw new \Exception(
                    "Doctrine entity class '$entityName' not found."
                );
            }
        } elseif ($dataSource === DataSourceType::CLASSES) {
            if (!$entityName || !class_exists($entityName)) {
                throw new \Exception("Class '$entityName' not found.");
            }
        } else {
            throw new \InvalidArgumentException(
                "Unknown data source: $dataSource"
            );
        }

        return new self($entityManager, $entityName, $dataSource);
    }

    public function setMode(string $mode)
    {
        $modes = ["default", "report"];

        if (!in_array($mode, $modes)) {
            throw new InvalidArgumentException("Invalid mode: $mode");
        }

        $this->mode = $mode;
        return $this;
    }

    public function setEditButtonConditionCallback(callable $callback)
    {
        $this->editButtonConditionCallback = $callback;
        return $this;
    }

    public function setEditButtonConditions(array $conditions)
    {
        $this->editButtonConditions = $conditions;
        return $this;
    }

    public function setDeleteButtonConditions(array $conditions)
    {
        $this->deleteButtonConditions = $conditions;
        return $this;
    }


    public function setDeleteButtonConditionCallback(callable $callback)
    {
        $this->deleteButtonConditionCallback = $callback;
        return $this;
    }


    public function setCustomAddFormRenderer(callable $renderer): self
    {
        $this->customAddFormRenderer = $renderer;
        return $this;
    }

    public function setCustomEditFormRenderer(callable $renderer): self
    {
        $this->customEditFormRenderer = $renderer;
        return $this;
    }

    public function setCustomViewFormRenderer(callable $renderer): self
    {
        $this->customViewFormRenderer = $renderer;
        return $this;
    }

    public function setCustomAddAction(string $url): self
    {
        $this->customAddAction = $url;
        return $this;
    }

    public function setCustomEditAction(string $url): self
    {
        $this->customEditAction = $url;
        return $this;
    }

    public function setCustomAddFormHeader(string $header): self
    {
        $this->customAddFormHeader = $header;
        return $this;
    }

    public function setCustomEditFormHeader(string $header): self
    {
        $this->customEditFormHeader = $header;
        return $this;
    }

    public function setCustomViewFormHeader(string $header): self
    {
        $this->customViewFormHeader = $header;
        return $this;
    }

    public function setClassFetchDataFunction(string $functionName): self
    {
        $this->classFetchDataFunction = $functionName;
        return $this;
    }

    public function setRowDataTransformer(callable $callback): self
    {
        $this->rowDataTransformer = $callback;
        return $this;
    }

    public function addColumnBeforeActions(
        string   $key,
        callable $callback,
        bool     $raw = false,
        callable $visibleWhen = null
    ): self
    {
        $this->columnsBeforeActions[$key] = [
            "callback" => $callback,
            "raw" => $raw,
            "visibleWhen" => $visibleWhen,
        ];
        return $this;
    }

    public function addColumnAfterActions(
        string   $key,
        callable $callback,
        bool     $raw = false,
        callable $visibleWhen = null
    ): self
    {
        $this->columnsAfterActions[$key] = [
            "callback" => $callback,
            "raw" => $raw,
            "visibleWhen" => $visibleWhen,
        ];
        return $this;
    }

    private function renderCustomColumn(array $config, array $row): string
    {
        if (isset($config["visibleWhen"])) {
            $visible = $config["visibleWhen"];

            if (is_callable($visible)) {
                if (!call_user_func($visible, $row)) {
                    return "";
                }
            } elseif ($visible === false) {
                return "";
            }
        }

        $value = call_user_func($config["callback"], $row);

        return !empty($config["raw"])
            ? $value
            : htmlspecialchars((string)$value);
    }

    private function evaluateCondition($row, $field, $operator, $value)
    {
        switch ($operator) {
            case "=":
                return $row[$field] == $value;
            case "!=":
                return $row[$field] != $value;
            case ">":
                return $row[$field] > $value;
            case "<":
                return $row[$field] < $value;

            default:
                return false;
        }
    }

    private function evaluateEditConditions($row)
    {
        $conditions = $this->editButtonConditions;

        if (isset($conditions["groups"]) && !empty($conditions["groups"])) {
            return $this->evaluateGroupedConditions(
                $row,
                $conditions["groups"],
                $conditions["group_operator"] ?? "AND"
            );
        }

        return $this->evaluateFlatConditions(
            $row,
            $conditions["conditions"],
            $conditions["group_operator"] ?? "AND"
        );
    }

    private function evaluateDeleteConditions($row)
    {
        $conditions = $this->deleteButtonConditions;

        if (isset($conditions["groups"]) && !empty($conditions["groups"])) {
            return $this->evaluateGroupedConditions(
                $row,
                $conditions["groups"],
                $conditions["group_operator"] ?? "AND"
            );
        }

        return $this->evaluateFlatConditions(
            $row,
            $conditions["conditions"],
            $conditions["group_operator"] ?? "AND"
        );
    }

    private function evaluateGroupedConditions($row, $groups, $topLevelOperator)
    {
        $isEditButtonVisible = $topLevelOperator === "AND" ? true : false;

        foreach ($groups as $group) {
            $groupOperator = $group["group_operator"] ?? "AND";
            $groupConditions = $group["conditions"];

            $groupResult = $this->evaluateFlatConditions(
                $row,
                $groupConditions,
                $groupOperator
            );

            if ($topLevelOperator === "AND") {
                $isEditButtonVisible = $isEditButtonVisible && $groupResult;
            } elseif ($topLevelOperator === "OR") {
                $isEditButtonVisible = $isEditButtonVisible || $groupResult;
            }
        }

        return $isEditButtonVisible;
    }

    private function evaluateFlatConditions(
        $row,
        $conditions,
        $logicOperator = "AND"
    )
    {
        $result = $logicOperator === "AND";

        foreach ($conditions as $condition) {
            $field = $condition["field"];
            $comparisonOperator = $condition["operator"];
            $value = $condition["value"];

            $isValid = $this->evaluateCondition(
                $row,
                $field,
                $comparisonOperator,
                $value
            );

            if ($logicOperator === "AND") {
                $result = $result && $isValid;
                if (!$result) {
                    break;
                }
            } else {
                $result = $result || $isValid;
                if ($result) {
                    break;
                }
            }
        }

        return $result;
    }

    public function normalizeColumns(array $columns): array
    {
        $normalized = [];

        foreach ($columns as $key => $value) {
            if (is_int($key)) {
                $normalized[$value] = [
                    "type" => "input",
                    "input_type" => "text",
                    "label" => ucfirst(str_replace("_", " ", $value)),
                ];
            } elseif (is_array($value)) {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

    public function setDataTableButtons(array $buttons)
    {
        $this->dataTableButtons = $buttons;
        return $this;
    }

    public function setAddPermission(bool $permission)
    {
        $this->canAdd = $permission;
        return $this;
    }

    public function columnToBeAdded(array $columns)
    {
        $columns = $this->normalizeColumns($columns);
        $this->columnsToAdd = $columns;
        return $this;
    }

    public function columnsToBeEdited(array $columns)
    {
        $columns = $this->normalizeColumns($columns);
        $this->columnsToEdit = $columns;
        return $this;
    }

    public function tablesToJoin(array $tables)
    {
        $this->tablesToJoin = $tables;
        return $this;
    }

    public function valuesToSelect(array $values)
    {
        $this->valuesToSelect = $values;
        return $this;
    }

    public function valuesToRender(array $values)
    {
        $this->valuesToRender = $values;
        return $this;
    }

    public function valuesToShowonModal(array $values)
    {
        $this->valuesToShowonModal = $values;
        return $this;
    }

    private function beautifyColumnName(string $column): string
    {
        return ucwords(str_replace("_", " ", $column));
    }

    public function enablePagination(
        int   $recordsPerPage = 10,
        array $columns = []
    )
    {
        $this->paginationEnabled = true;
        $this->recordsPerPage = $recordsPerPage;
        $this->paginateColumns = $columns;
        return $this;
    }

    private function getTotalRecords(): int
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select("COUNT(e.id)")->from($this->entityName, "e");

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    public function setAddButtonLabel(string $label)
    {
        $this->addButtonLabel = $label;
        return $this;
    }

    public function setTableId(string $id)
    {
        $this->tableId = $id;
        return $this;
    }

    private function generateDataTableButtons()
    {
        $buttons = [];

        foreach ($this->dataTableButtons as $type => $config) {
            if (is_array($config)) {
                $button = array_merge(
                    [
                        "extend" => $type,
                        "text" => ucfirst($type),
                    ],
                    $config
                );
            } else {
                $button = [
                    "extend" => $type,
                    "text" => ucfirst($type),
                ];
            }

            $buttons[] = $button;
        }

        return $buttons;
    }

    private function fetchData($page = 1)
    {
        switch ($this->dataSource) {
            case DataSourceType::DOCTRINE:
                $qb = $this->entityManager->createQueryBuilder();
                $qb->select($this->valuesToSelect)->from(
                    $this->entityName,
                    "e"
                );

                foreach ($this->tablesToJoin as $join) {
                    $qb->leftJoin(
                        $join["table"],
                        $join["alias"],
                        "WITH",
                        $join["on"]
                    );
                }

                foreach ($this->searchableColumns as $column => $config) {
                    $postKey = str_replace(".", "_", $column);
                    if (!empty($_POST[$postKey])) {
                        $paramKey = str_replace(".", "_", $column);
                        $table = $config["table"] ?? "e";
                        $columnName = $config["column"];
                        $operator = $config["operator"] ?? "=";

                        if ($operator === "LIKE") {
                            $qb->andWhere("$table.$columnName LIKE :$paramKey");
                            $qb->setParameter(
                                $paramKey,
                                "%" . $_POST[$column] . "%"
                            );
                        } elseif ($operator === "=") {
                            $qb->andWhere("$table.$columnName = :$paramKey");
                            $qb->setParameter($paramKey, $_POST[$column]);
                        } elseif ($operator === "BETWEEN") {
                            $range = $_POST[$column];
                            if ($range) {
                                list($start, $end) = explode(",", $range);
                                $qb->andWhere(
                                    "$table.$columnName BETWEEN :start AND :end"
                                );
                                $qb->setParameter(
                                    "start",
                                    $start
                                )->setParameter("end", $end);
                            }
                        } else {
                            $qb->andWhere("$table.$columnName = :$paramKey");
                            $qb->setParameter($paramKey, $_POST[$column]);
                        }
                    }
                }

                if ($this->paginationEnabled) {
                    $offset = ($page - 1) * $this->recordsPerPage;
                    $qb->setFirstResult($offset)->setMaxResults(
                        $this->recordsPerPage
                    );
                }

                $query = $qb->getQuery();
                return $query->getArrayResult();
                break;

            case DataSourceType::CLASSES:
                $className = $this->entityName;
                $raw = $this->classFetchDataFunction;
                [$methodName, $argString] = array_pad(
                    explode(":", $raw, 2),
                    2,
                    ""
                );

                $args =
                    $argString !== ""
                        ? array_map("trim", explode(",", $argString))
                        : [];

                if (!class_exists($className)) {
                    throw new \Exception("Class '$className' does not exist.");
                }

                if (!method_exists($className, $methodName)) {
                    throw new \Exception(
                        "Method '$methodName' does not exist in class '$className'."
                    );
                }

                $reflection = new \ReflectionMethod($className, $methodName);

                if (!$reflection->isPublic()) {
                    throw new \Exception(
                        "Method '$methodName' is not public in class '$className'."
                    );
                }

                if ($reflection->isStatic()) {
                    $result = $className::$methodName(...$args);
                } else {
                    $instance = new $className();
                    $result = $instance->$methodName(...$args);
                }

                return $result;
                break;

            default:
                throw new \Exception("Unknown Data Source.");
                break;
        }
    }

    public function renderDataTable($page = 1)
    {
        $data = $this->fetchData();
        $addModalId = $this->tableId . "_addModal";
        $addFormId = $this->tableId . "_addForm";
        $totalRecords = $this->paginationEnabled
            ? $this->getTotalRecords()
            : count($data);
        $totalPages = $this->paginationEnabled
            ? ceil($totalRecords / $this->recordsPerPage)
            : 1;
        if (!empty($this->searchableColumns)) {
            echo '<form method="POST" class="row g-3 mb-3">';

            foreach ($this->searchableColumns as $column => $config) {
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
                                  <option value=''>-- Select --</option>";
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

        if ($this->mode === "default") {
            if ($this->canAdd) {
                echo '<button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#' .
                    $addModalId .
                    '">' .
                    htmlspecialchars($this->addButtonLabel) .
                    "</button>";
            }
        }

        echo "<table id='" .
            htmlspecialchars($this->tableId) .
            "' class='table table-striped table-bordered'>";
        echo "<thead><tr>";

        echo "<th>#</th>";

        $columnsToRender = empty($this->valuesToRender)
            ? $this->valuesToSelect
            : $this->valuesToRender;
        foreach ($columnsToRender as $column) {
            if (stripos($column, " as ") !== false) {
                $parts = preg_split("/\s+as\s+/i", $column);
                $label = $this->beautifyColumnName($parts[1]);
            } else {
                $dotSplit = explode(".", $column);
                $lastPart = end($dotSplit);

                $label = $this->beautifyColumnName($lastPart);
            }
            echo "<th>$label</th>";
        }

        foreach ($this->columnsBeforeActions as $key => $callback) {
            echo "<th>" . $this->beautifyColumnName($key) . "</th>";
        }

        if ($this->mode === "default") {
            echo "<th>Actions</th>";
        }

        foreach ($this->columnsAfterActions as $key => $callback) {
            echo "<th>" . $this->beautifyColumnName($key) . "</th>";
        }

        echo "</tr></thead>";
        echo "<tbody>";

        $index = 1;
        foreach ($data as $row) {
            if ($this->rowDataTransformer) {
                $row = call_user_func($this->rowDataTransformer, $row);
            }
            echo "<tr>";
            echo "<td>$index</td>";

            foreach ($columnsToRender as $column) {
                if (stripos($column, " as ") !== false) {
                    $parts = preg_split("/\s+as\s+/i", $column);
                    $key = trim($parts[1]);
                } else {
                    $dotSplit = explode(".", $column);
                    $key = end($dotSplit);
                }

                echo "<td>{$row[$key]}</td>";
            }

            $canEdit = true;
            $canDelete = true;

            if ($this->editButtonConditionCallback) {
                $canEdit = call_user_func(
                    $this->editButtonConditionCallback,
                    $row
                );
            } elseif (!empty($this->editButtonConditions)) {
                $canEdit = $this->evaluateEditConditions($row);
            }

            if ($this->deleteButtonConditionCallback) {
                $canDelete = call_user_func(
                    $this->deleteButtonConditionCallback,
                    $row
                );
            } elseif (!empty($this->deleteButtonConditions)) {
                $canDelete = $this->evaluateDeleteConditions($row);
            }

            foreach ($this->columnsBeforeActions as $key => $conf) {
                echo "<td>" . $this->renderCustomColumn($conf, $row) . "</td>";
            }

            if ($this->mode === "default") {

                switch ($this->actionsButtonMode) {
                    case ActionsButtonMode::DEFAULT:
                        echo "<td>";
                        if ($canEdit) {
                            echo "<button class='btn btn-primary btn-sm editBtn' data-id='{$row["id"]}'";
                            foreach ($this->columnsToEdit as $columnName => $config) {
                                $value =
                                    isset($row[$columnName]) &&
                                    is_scalar($row[$columnName])
                                        ? htmlspecialchars($row[$columnName])
                                        : "";
                                echo " data-$columnName='$value'";
                            }
                            echo ">Edit</button> ";
                        }
                        $link = $this->viewLink . $row['id'];

                        $columnsToRenderonModal = empty($this->valuesToShowonModal) ? $this->valuesToRender : $this->valuesToShowonModal;
                        switch ($this->viewSource) {
                            case ViewSource::LINK:
                                echo "<a href='{$link}' class='btn btn-success btn-sm' target='_blank'>View</a>";
                                break;
                            case ViewSource::MODAL:
                                echo "<button class='btn btn-success btn-sm viewBtn' data-id='{$row["id"]}'";
                                foreach ($columnsToRenderonModal as $columnName => $config) {
                                    $value =
                                        isset($row[$columnName]) &&
                                        is_scalar($row[$columnName])
                                            ? htmlspecialchars($row[$columnName])
                                            : "";
                                    echo " data-$columnName='$value'";
                                }
                                echo ">View</button>";
                                break;
                        }


                        echo $canDelete ? "
                      <button class='btn btn-danger btn-sm deleteBtn' data-id='{$row["id"]}'>Delete</button>
                    </td>" : "</td>";

                        break;

                    case ActionsButtonMode::DROPDOWN:
                        echo "<td>";
                        echo "<div class='btn btn-light dropdown hover-dropdown' role='button' data-bs-toggle='dropdown'>";
                        echo "<a href='#' style='text-decoration: none !important;' class='ellipsis-trigger' aria-expanded='false'>";
                        echo "&#8942;";
                        echo "</a>";
                        echo "<ul class='dropdown-menu'>";

                        if ($canEdit) {
                            echo "<li><a class='dropdown-item editBtn' href='#' data-id='{$row["id"]}'";
                            foreach ($this->columnsToEdit as $columnName => $config) {
                                $value = isset($row[$columnName]) && is_scalar($row[$columnName])
                                    ? htmlspecialchars($row[$columnName])
                                    : "";
                                echo " data-$columnName='$value'";
                            }
                            echo ">Edit</a></li>";
                        }

                        $link = $this->viewLink . $row['id'];

                        $columnsToRenderonModal = empty($this->valuesToShowonModal) ? $this->valuesToRender : $this->valuesToShowonModal;

                        switch ($this->viewSource) {
                            case ViewSource::LINK:
                                echo "<li><a class='dropdown-item' href='{$link}' data-id='{$row["id"]}'>View</a></li>";
                                break;

                            case ViewSource::MODAL:

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
                                break;
                        }

                        echo $canDelete ? "<li><a class='dropdown-item deleteBtn' href='#' data-id='{$row["id"]}'>Delete</a></li>" : "";
                        echo "</ul></div>";
                        echo "</td>";
                        break;


                }


            }

            foreach ($this->columnsAfterActions as $key => $conf) {
                echo "<td>" . $this->renderCustomColumn($conf, $row) . "</td>";
            }

            echo "</tr>";
            $index++;
        }

        echo "</tbody></table>";

        $this->renderModalsAndScripts();
    }

    private function renderModalsAndScripts()
    {
        $addFormId = $this->tableId . "_addForm";
        $editFormId = $this->tableId . "_editForm";
        $addModalId = $this->tableId . "_addModal";
        $editModalId = $this->tableId . "_editModal";
        $viewModalId = $this->tableId . "_viewModal";
        $viewFormId = $this->tableId . "_viewForm";

        // --- Add Modal ---
        echo '
    <div class="modal fade" id="' .
            $addModalId .
            '" tabindex="-1" aria-labelledby="' .
            $addModalId .
            'Label" aria-hidden="true">
            <div class="modal-dialog ' . $this->addDialogSize . '">
        <div class="modal-content">
          <form id="' .
            $addFormId .
            '" method="POST" action="' .
            htmlspecialchars($this->customAddAction ?? "") .
            '">
            <input type="hidden" name="' .
            ($this->customAddFormRenderer ? "token" : "csrf_token") .
            '" value="' .
            htmlspecialchars($this->token) .
            '">
            <input type="hidden" name="form_action" value="add">
            <div class="modal-header">
              <h5 class="modal-title" id="' .
            $addModalId .
            'Label">' .
            htmlspecialchars($this->customAddFormHeader) .
            '</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">';

        if ($this->customAddFormRenderer) {
            call_user_func($this->customAddFormRenderer);
        } else {
            foreach ($this->columnsToAdd as $column => $config) {
                $label = $config["label"] ?? ucfirst($column);
                if ($config["type"] === "input") {
                    $inputType = $config["input_type"] ?? "text";
                    echo "<div class='mb-3'>
                        <label for='$column' class='form-label'>$label</label>
                        <input type='$inputType' class='form-control' id='$column' name='$column' required>
                      </div>";
                } elseif ($config["type"] === "select") {
                    echo "<div class='mb-3'>
                        <label for='$column' class='form-label'>$label</label>
                        <select class='form-control' id='$column' name='$column' required>";
                    foreach ($config["options"] as $option) {
                        $value = $option[$config["value_field"]] ?? $option;
                        $label = $option[$config["label_field"]] ?? $option;
                        echo "<option value='$value'>$label</option>";
                    }
                    echo "</select></div>";
                }
            }
        }

        echo '  </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Add</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>';

        // --- Edit Modal ---
        echo '
    <div class="modal fade" id="' .
            $editModalId .
            '" tabindex="-1" aria-labelledby="' .
            $editModalId .
            'Label" aria-hidden="true">
            <div class="modal-dialog ' . $this->editDialogSize . '">
        <div class="modal-content">
          <form id="' .
            $editFormId .
            '" method="POST" action="' .
            htmlspecialchars($this->customEditAction ?? "") .
            '">
            <input type="hidden" name="' .
            ($this->customEditFormRenderer ? "token" : "csrf_token") .
            '" value="' .
            htmlspecialchars($this->token) .
            '">
            <input type="hidden" id="' .
            $editModalId .
            'editId" name="id" value="">

            <input type="hidden" name="form_action" value="edit">
            <div class="modal-header">
              <h5 class="modal-title" id="' .
            $editModalId .
            'Label">' .
            $this->customEditFormHeader .
            '</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="' .
            $editFormId .
            'Body">';

        if (!$this->customEditFormRenderer) {
        } else {
            call_user_func($this->customEditFormRenderer);
        }

        echo '  </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Save changes</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>';

        // View Modal
        echo '
    <div class="modal fade" id="' .
            $viewModalId .
            '" tabindex="-1" aria-labelledby="' .
            $viewModalId .
            'Label" aria-hidden="true">
            <div class="modal-dialog ' . $this->viewDialogSize . '">
        <div class="modal-content">
          <form id="' .
            $viewFormId .
            '" method="POST" action="' .
            htmlspecialchars($this->customEditAction ?? "") .
            '">
            <input type="hidden" name="' .
            ($this->customViewFormRenderer ? "token" : "csrf_token") .
            '" value="' .
            htmlspecialchars($this->token) .
            '">
            <input type="hidden" id="' .
            $viewModalId .
            'editId" name="id" value="">

            <input type="hidden" name="form_action" value="edit">
            <div class="modal-header">
              <h5 class="modal-title" id="' .
            $viewModalId .
            'Label">' .
            $this->customViewFormHeader .
            '</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="' .
            $viewFormId .
            'Body">';

        if (!$this->customViewFormRenderer) {
        } else {
            call_user_func($this->customViewFormRenderer);
        }

        //              <button type="submit" class="btn btn-primary">Save changes</button>
//              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        echo '  </div>
            <div class="modal-footer">

            </div>
          </form>
        </div>
      </div>
    </div>';

        // --- Scripts ---
        echo '<script>
    $(document).ready(function () {
        var tableId = "#' .
            $this->tableId .
            '";

        if ($(tableId).length) {
            $(tableId).DataTable({
                dom: "Bfrtip",
                buttons: ' .
            json_encode($this->generateDataTableButtons()) .
            '
            });
        }

        $(".viewBtn").click(function() {
            var id = $(this).data("id");

            ';

        $columnsToRender = empty($this->valuesToShowonModal) ? $this->valuesToRender : $this->valuesToShowonModal;
        if (!$this->customViewFormRenderer) {
            echo '$("#' . $viewFormId . 'Body").empty();';
        }
        if ($this->customViewFormRenderer) {
            foreach ($columnsToRender as $column => $config) {
                echo '$("#' .
                    $viewModalId .
                    $column .
                    '").val($(this).data("' .
                    $column .
                    '"));' .
                    "\n";
            }
            echo '$("#' . $viewModalId . 'viewId").val(id);';
        } else {
            foreach ($columnsToRender as $column => $config) {
                $label = $config["label"] ?? ucfirst($column);
                $selectLabel = htmlspecialchars(
                    $config["label"] ?? ucfirst($column),
                    ENT_QUOTES
                );
                echo "var $column = $(this).data('$column');";
                if ($config["type"] === "input") {
                    $inputType = $config["input_type"] ?? "text";
                    echo '$("#' .
                        $viewFormId .
                        'Body").append(
                    `<div class="mb-3">
                        <label for="' .
                        $column .
                        '" class="form-label">' .
                        $label .
                        '</label>
                        <input type="' .
                        $inputType .
                        '" class="form-control" id="' .
                        $column .
                        '" name="' .
                        $column .
                        '" value="` + ' .
                        $column .
                        ' + `" required>
                    </div>`
                );';
                } elseif ($config["type"] === "select") {
                    echo "var selectedVal = " . $column . ";";
                    echo "var options = `";
                    foreach ($config["options"] as $option) {
                        $value = htmlspecialchars(
                            $option[$config["value_field"]] ?? $option,
                            ENT_QUOTES
                        );
                        $label = htmlspecialchars(
                            $option[$config["label_field"]] ?? $option,
                            ENT_QUOTES
                        );
                        echo "<option value='$value' \${selectedVal == \"$value\" ? 'selected' : ''}>$label</option>";
                    }
                    echo "`;";
                    echo '$("#' .
                        $viewFormId .
                        'Body").append(
                    `<div class="mb-3">
                        <label for="' .
                        $column .
                        '" class="form-label">' .
                        $selectLabel .
                        '</label>
                        <select class="form-control" id="' .
                        $column .
                        '" name="' .
                        $column .
                        '" required>` + options + `</select>
                    </div>`
                );
                $("#' .
                        $column .
                        '").val(' .
                        $column .
                        ");";
                }
            }

            echo '$("#' . $viewModalId . 'viewId").val(id);';
        }

        echo '
            var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("' .
            $viewModalId .
            '"));
            modal.show();
        });
        
            $(".editBtn").click(function() {
            var id = $(this).data("id");

            ';

        if (!$this->customEditFormRenderer) {
            echo '$("#' . $editFormId . 'Body").empty();';
        }
        if ($this->customEditFormRenderer) {
            foreach ($this->columnsToEdit as $column => $config) {
                echo '$("#' .
                    $editModalId .
                    $column .
                    '").val($(this).data("' .
                    $column .
                    '"));' .
                    "\n";
            }
            echo '$("#' . $editModalId . 'editId").val(id);';
        } else {
            foreach ($this->columnsToEdit as $column => $config) {
                $label = $config["label"] ?? ucfirst($column);
                $selectLabel = htmlspecialchars(
                    $config["label"] ?? ucfirst($column),
                    ENT_QUOTES
                );
                echo "var $column = $(this).data('$column');";
                if ($config["type"] === "input") {
                    $inputType = $config["input_type"] ?? "text";
                    echo '$("#' .
                        $editFormId .
                        'Body").append(
                    `<div class="mb-3">
                        <label for="' .
                        $column .
                        '" class="form-label">' .
                        $label .
                        '</label>
                        <input type="' .
                        $inputType .
                        '" class="form-control" id="' .
                        $column .
                        '" name="' .
                        $column .
                        '" value="` + ' .
                        $column .
                        ' + `" required>
                    </div>`
                );';
                } elseif ($config["type"] === "select") {
                    echo "var selectedVal = " . $column . ";";
                    echo "var options = `";
                    foreach ($config["options"] as $option) {
                        $value = htmlspecialchars(
                            $option[$config["value_field"]] ?? $option,
                            ENT_QUOTES
                        );
                        $label = htmlspecialchars(
                            $option[$config["label_field"]] ?? $option,
                            ENT_QUOTES
                        );
                        echo "<option value='$value' \${selectedVal == \"$value\" ? 'selected' : ''}>$label</option>";
                    }
                    echo "`;";
                    echo '$("#' .
                        $editFormId .
                        'Body").append(
                    `<div class="mb-3">
                        <label for="' .
                        $column .
                        '" class="form-label">' .
                        $selectLabel .
                        '</label>
                        <select class="form-control" id="' .
                        $column .
                        '" name="' .
                        $column .
                        '" required>` + options + `</select>
                    </div>`
                );
                $("#' .
                        $column .
                        '").val(' .
                        $column .
                        ");";
                }
            }

            echo '$("#' . $editModalId . 'editId").val(id);';
        }

        echo '
            var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("' .
            $editModalId .
            '"));
            modal.show();
        });

        $(".deleteBtn").click(function() {
            var id = $(this).data("id");
            Swal.fire({
        title: "Are you sure?",
        text: "You won\'t be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
                const form = $("<form>", {
                    method: "POST",
                    action: ""
                });

                form.append($("<input>", {
                    type: "hidden",
                    name: "form_action",
                    value: "delete"
                }));

                form.append($("<input>", {
                    type: "hidden",
                    name: "id",
                    value: id
                }));

                form.append($("<input>", {
                    type: "hidden",
                    name: "' .
            ($this->customEditFormRenderer ? "token" : "csrf_token") .
            '",
                    value: "' .
            $this->token .
            '"
                }));

                $("body").append(form);
                form.submit();
            }
            })
        });
    });
    </script>';
    }

    public function handleDelete($id)
    {
        $repository = $this->entityManager->getRepository($this->entityName);
        $entity = $repository->find($id);

        if ($entity) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }

        $this->echoSwal("Success", "Record deleted successfully.", "success");
    }

    private function handleAdd(array $data)
    {
        $entityClass = $this->entityName;
        $entity = new $entityClass();

        foreach ($this->columnsToAdd as $column => $config) {
            $value = $data[$column] ?? null;
            $setter = "set" . ucfirst($column);
            if (method_exists($entity, $setter)) {
                $entity->$setter($value);
            }
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        echo "
         <script>
             Swal.fire({
                 icon: 'success',
                 title: 'Record added successfully!',
                 text: 'Your record has been added to the database.',
                 showConfirmButton: true
             }).then((result) => {
                 if (result.isConfirmed) {

                 }
             });
         </script>";
    }

    private function handleEdit(array $data)
    {
        $id = $data["id"] ?? null;
        if (!$id) {
            $this->echoSwal("Error", "No ID provided for edit.", "error");
            return;
        }

        $repository = $this->entityManager->getRepository($this->entityName);
        $entity = $repository->find($id);

        if (!$entity) {
            $this->echoSwal("Error", "Record not found.", "error");
            return;
        }

        foreach ($this->columnsToEdit as $column => $config) {
            $value = $data[$column] ?? null;
            $setter = "set" . ucfirst($column);
            if (method_exists($entity, $setter)) {
                $entity->$setter($value);
            }
        }

        $this->entityManager->flush();
        $this->echoSwal("Success", "Record updated successfully.", "success");
    }

    private function echoSwal(
        string $title,
        string $message,
        string $icon = "success"
    )
    {
        echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                title: "' .
            addslashes($title) .
            '",
                text: "' .
            addslashes($message) .
            '",
                icon: "' .
            $icon .
            '",
                confirmButtonText: "OK"
            }).then(function () {

            });
        });
    </script>';
    }

    public function generateCsrfToken($length = 32)
    {
        $token = "";
        for ($i = 0; $i < $length; $i++) {
            $token .= rand(0, 9);
        }
        return $token;
    }

    public function handleRequest(array $postData)
    {
        if (
            !isset($postData["csrf_token"]) &&
            !isset($postData["form_action"])
        ) {
            return;
        }
        $action = $postData["form_action"] ?? null;

        switch ($action) {
            case "add":
                $this->handleAdd($postData);
                break;
            case "edit":
                $this->handleEdit($postData);
                break;
            case "delete":
                $this->handleDelete($postData["id"]);
                break;
            default:
                echo "Invalid action.";
        }
    }
}
