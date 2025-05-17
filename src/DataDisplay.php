<?php

namespace Revaycolizer\DataDisplay;

use App\Helpers\ActionButtons;
use App\Helpers\AddButton;
use App\Helpers\ButtonsViewable;
use App\Helpers\DataTableScript;
use App\Helpers\Form;
use App\Helpers\Modals;
use App\Helpers\Searchable;
use App\Helpers\SweetAlertHandle;
use App\Helpers\UiNoAccessButton;
use App\Types\ActionsButtonMode;
use App\Types\BootStrap;
use App\Types\DataDisplayModes;
use App\Types\DataSourceType;
use App\Types\DialogSizes;
use App\Types\SweetAlert;
use App\Types\ViewSource;
use App\Types\Buttons;
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
    private $mode = DataDisplayModes::DEFAULT;
    private $addDialogSize='modal-fullscreen';
    private $editDialogSize='modal-fullscreen';

    private $viewDialogSize='modal-fullscreen';

    private $actionsButtonMode = ActionsButtonMode::DEFAULT;

    private $viewSource = ViewSource::LINK;

    private $viewLink;

    private $valuesToShowonModal = [];

    private $bootstrap = BootStrap::V5;

    private $sweetAlert = SweetAlert::V2;

    private $buttonsViewable = [Buttons::ADD, Buttons::EDIT, Buttons::VIEW, Buttons::DELETE];

    private $addAction;

    private $editAction;

    private $deleteAction;

    private $deleteTitle = "Delete Record";

    private $deleteMessage = "Are you sure you want to delete this record?";

    private $delayDataTable = false;

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

    public function setAddAction(string $action): self
    {
        $this->addAction = $action;
        return $this;
    }

    public function setDeleteAction(string $action): self
    {
        $this->deleteAction = $action;
        return $this;
    }

    public function setDelayDataTable(bool $delayDataTable): self
    {
        $this->delayDataTable = $delayDataTable;
        return $this;
    }

    public function setDeleteTitle(string $title): self
    {
        $this->deleteTitle = $title;
        return $this;
    }

    public function setDeleteMessage(string $title): self
    {
        $this->deleteMessage = $title;
        return $this;
    }

    public function setEditAction(string $action): self
    {
        $this->editAction = $action;
        return $this;
    }

    public function setViewLink($link): self
    {
        $this->viewLink = $link;
        return $this;
    }

    public function setBootStrapVersion(BootStrap $bootstrap): self
    {
        $this->bootstrap = $bootstrap;
        return $this;
    }

    /**
     * @param Buttons[] $buttons
     */
    public function setButtonsViewable(array $buttons): self
    {
        $this->buttonsViewable = $buttons;
        return $this;
    }


    public function setSweetAlertVersion(SweetAlert $sweetAlert): self
    {
        $this->sweetAlert = $sweetAlert;
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

    public function setAddDialogSize(DialogSizes $addDialogSize)
    {
        $this->addDialogSize = $addDialogSize->value;
        return $this;
    }

    public function setEditDialogSize(DialogSizes $editDialogSize)
    {
        $this->editDialogSize = $editDialogSize->value;
        return $this;
    }

    public function setViewDialogSize(DialogSizes $viewDialogSize)
    {
        $this->viewDialogSize = $viewDialogSize->value;
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

    public function setMode(DataDisplayModes $mode = DataDisplayModes::DEFAULT): self
    {
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

        Searchable::show($this->bootstrap, $this->searchableColumns);

        if (ButtonsViewable::add($this->buttonsViewable)) {
            if ($this->mode === DataDisplayModes::DEFAULT) {
                if ($this->canAdd) {

                    AddButton::show($this->bootstrap, $this->addButtonLabel, $addModalId);

                } else {
                    UiNoAccessButton::show($this->bootstrap, $this->addButtonLabel);
                }
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
                $alias = trim($parts[1]);

                if (strtolower($alias) === 'id') {
                    continue;
                }

                $label = $this->beautifyColumnName($alias);
            } else {
                $dotSplit = explode(".", $column);
                $lastPart = trim(end($dotSplit));

                if (strtolower($lastPart) === 'id') {
                    continue;
                }

                $label = $this->beautifyColumnName($lastPart);
            }

            echo "<th>$label</th>";
        }

        foreach ($this->columnsBeforeActions as $key => $callback) {
            echo "<th>" . $this->beautifyColumnName($key) . "</th>";
        }

        if ($this->mode === DataDisplayModes::DEFAULT) {
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
            echo "<td style='width: 1%; white-space: nowrap;'>$index</td>";

            foreach ($columnsToRender as $column) {
                if (stripos($column, " as ") !== false) {
                    $parts = preg_split("/\s+as\s+/i", $column);
                    $key = trim($parts[1]);
                    if (strtolower($key) === 'id') {
                        continue;
                    }
                } else {
                    $dotSplit = explode(".", $column);
                    $key = end($dotSplit);

                    if (strtolower($key) === 'id') {
                        continue;
                    }
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

            if ($this->mode === DataDisplayModes::DEFAULT) {

                switch ($this->actionsButtonMode) {
                    case ActionsButtonMode::DEFAULT:
                        $link = $this->viewLink . $row['id'];

                        $columnsToRenderonModal = empty($this->valuesToShowonModal) ? $this->valuesToRender : $this->valuesToShowonModal;

                        ActionButtons::default($this->bootstrap, $link, $row['id'], $canEdit
                            , $this->columnsToEdit, $this->viewSource, $columnsToRenderonModal, $canDelete, $row, $this->buttonsViewable);
                        break;

                    case ActionsButtonMode::DROPDOWN:
                        $link = $this->viewLink . $row['id'];

                        $columnsToRenderonModal = empty($this->valuesToShowonModal) ? $this->columnsToAdd : $this->valuesToShowonModal;

                        ActionButtons::show($this->bootstrap, $link, $row['id'], $canEdit
                            , $this->columnsToEdit, $this->viewSource, $columnsToRenderonModal, $canDelete, $row, $this->buttonsViewable);
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
        Form::Addform($this->bootstrap, $addModalId, $this->addDialogSize, $addFormId,
            $this->customAddAction ?? "", $this->customAddFormRenderer,
            $this->token, $this->customAddFormHeader, $this->addAction
        );

        if ($this->customAddFormRenderer) {
            call_user_func($this->customAddFormRenderer);
        } else {
            foreach ($this->columnsToAdd as $column => $config) {
                $label = $config["label"] ?? ucfirst($column);
                $required = !empty($config["required"]) ? "required" : "";
                if ($config["type"] === "input") {
                    $inputType = $config["input_type"] ?? "text";
                    $step = $config["step"] ?? null;
                    $min = $config["min"] ?? null;
                    $max = $config["max"] ?? null;

                    echo "<div class='mb-3'>
                <label for='$column' class='form-label'>$label</label>
                <input type='$inputType' class='form-control' id='$column' name='$column' " .
                        ($step ? "step='$step' " : "") .
                        ($min ? "min='$min' " : "") .
                        ($max ? "max='$max' " : "") .
                        "$required>
                         </div>";
                }  elseif ($config["type"] === "text-area") {

                    $rows = $config["rows"] ?? 4;
                    $cols = $config["cols"] ?? "";
                    echo "<div class='mb-3'>
                <label for='$column' class='form-label'>$label</label>
                <textarea class='form-control' id='$column' name='$column' rows='$rows' $required></textarea>
              </div>";
                }

                elseif ($config["type"] === "select") {
                    echo "<div class='mb-3'>
                        <label for='$column' class='form-label'>$label</label>
                        <select class='form-control' id='$column' name='$column' $required>";
                    echo "<option value='' disabled selected>Select $label</option>";
                    foreach ($config["options"] as $option) {
                        $value = $option[$config["value_field"]] ?? $option;
                        $label = $option[$config["label_field"]] ?? $option;
                        echo "<option value='$value'>$label</option>";
                    }
                    echo "</select></div>";
                }
            }
        }

        Form::AddFormFooter($this->bootstrap);

        // --- Edit Modal ---
        Form::editForm($this->bootstrap, $editModalId, $this->editDialogSize, $editFormId,
            $this->customEditAction ?? "", $this->customEditFormRenderer
            , $this->token, $this->customEditFormHeader, $this->editAction
        );

        if (!$this->customEditFormRenderer) {
        } else {
            call_user_func($this->customEditFormRenderer);
        }

        Form::EditFormFooter($this->bootstrap);

        // View Modal
        Form::viewForm($this->bootstrap, $viewModalId, $this->viewDialogSize, $viewFormId
            , $this->customViewFormRenderer, $this->token, $this->customViewFormHeader);

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

      ' . DataTableScript::render($this->tableId, $this->delayDataTable, $this->generateDataTableButtons()) . '

        $(".viewBtn").click(function(e) {
        e.preventDefault();
            var id = $(this).data("id");

            ';

        $columnsToRender = empty($this->valuesToShowonModal) ? $this->columnsToAdd : $this->valuesToShowonModal;
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
                $required = !empty($config["required"]) ? "required" : "";
                $selectLabel = htmlspecialchars(
                    $config["label"] ?? ucfirst($column),
                    ENT_QUOTES
                );
                echo "var $column = $(this).data('$column');";
                if ($config["type"] === "input") {
                    $inputType = $config["input_type"] ?? "text";
                    $step = isset($config['step']) ? 'step="' . $config['step'] . '" ' : '';
                    $min = isset($config['min']) ? 'min="' . $config['min'] . '" ' : '';
                    $max = isset($config['max']) ? 'max="' . $config['max'] . '" ' : '';
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
                        ' + `" ' .
                        $step . $min . $max . $required
                        . '>
                    </div>`
                );';
                }
                elseif ($config["type"] === "text-area") {
                    $rows = $config["rows"] ?? 4;

                                echo '$("#' .
                                    $viewFormId .
                                    'Body").append(
                    `<div class="mb-3">
                        <label for="' .
                                    $column .
                                    '" class="form-label">' .
                                    $label .
                                    '</label>
                        <textarea class="form-control" id="' .
                                    $column .
                                    '" name="' .
                                    $column .
                                    '" rows="' .
                                    $rows .
                                    '" ' .
                                    $required .
                                    '>` + ' .
                                    $column .
                                    ' + `</textarea>
                    </div>`
                );';
                }


                elseif ($config["type"] === "select") {
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
                        '" ' . $required . '>` + options + `</select>
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

        Modals::showViewModal($this->bootstrap, $viewModalId);

        echo '$(".editBtn").click(function(e) {
            e.preventDefault();
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
                $required = !empty($config["required"]) ? "required" : "";
                $selectLabel = htmlspecialchars(
                    $config["label"] ?? ucfirst($column),
                    ENT_QUOTES
                );
                echo "var $column = $(this).data('$column');";

                if ($config["type"] === "input") {
                    $inputType = $config["input_type"] ?? "text";
                    $step = isset($config['step']) ? 'step="' . $config['step'] . '" ' : '';
                    $min = isset($config['min']) ? 'min="' . $config['min'] . '" ' : '';
                    $max = isset($config['max']) ? 'max="' . $config['max'] . '" ' : '';

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
                        ' + `" ' .
                        $step . $min . $max . $required
                        . '>
                    </div>`
                );';
                }

                elseif ($config["type"] === "text-area") {
                    $rows = $config["rows"] ?? 4;

                    echo '$("#' .
                        $editFormId .
                        'Body").append(
                    `<div class="mb-3">
                        <label for="' .
                        $column .
                        '" class="form-label">' .
                        $label .
                        '</label>
                        <textarea class="form-control" id="' .
                        $column .
                        '" name="' .
                        $column .
                        '" rows="' .
                        $rows .
                        '" ' .
                        $required .
                        '>` + ' .
                        $column .
                        ' + `</textarea>
                    </div>`
                );';
                }


                elseif ($config["type"] === "select") {
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
                        '" ' . $required . '>` + options + `</select>
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

        Modals::showEditModal($this->bootstrap, $editModalId);

        SweetAlertHandle::delete($this->sweetAlert, $this->deleteTitle, $this->deleteMessage, $this->token, $this->deleteAction);
        echo '
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
        SweetAlertHandle::handle($this->sweetAlert, $title, $message, $icon);
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
