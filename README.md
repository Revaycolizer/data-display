### 📊 DataDisplay Component

The DataDisplay class is a dynamic PHP data table renderer built with Sweet Alert and Bootstrap. It supports adding,
editing, deleting, searching, joining tables, and pagination — all from a fluent interface.

### ✨ Features

🗃 Dynamic table rendering with Bootstrap

🔍 Searchable columns with support for input/select types

### 🔐 CRUD Operations With Permissions

### 🔄 Editable records with modals

### ➕ Add new records using modals

### ❌ Delete functionality with confirmation

### 🔗 Supports JOINs

### 📑 Pagination support

### 🔐 CSRF protection

### Instantiate the Component Using Doctrine For Data Fetching

```php
use Revaycolizer\Crud\DataDisplay;

$dataTable = DataDisplay::create($entityManager, User::class);
```

### Instantiate the Component Using Classes For Data fetching

```php
$dataDisplay = DataDisplay::create(null, Category::class,DataSourceType::CLASSES);
```

### Provide The Method Which Will be Used in Fetching Data When Using Classes

```php
$dataDisplay
   ->setClassFetchDataFunction("all")
```

### Provide The Method With It's Parameters Which Will be Used in Fetching Data When Using Classes

```php
$dataDisplay->setClassFetchDataFunction("all:param1,param2")
```

### DataDisplay Modes

## Mode 1: Default

By Default the mode is Default There is no Need to Set Mode

```php
->setMode(DataDisplayModes::DEFAULT)
```

## Mode 2: Report

If mode is set to report it will remove the CRUD Buttons(Add,Edit & Delete)

```php
 ->setMode(DataDisplayModes::REPORT)
```

### Action Button Modes

# Default

```php 
  ->setActionsButtonMode(ActionsButtonMode::DEFAULT)
```

# Dropdown

```php 
  ->setActionsButtonMode(ActionsButtonMode::DROPDOWN)
```

### 🔧 Configuration

Set Columns to Add

```php
$dataTable->columnToBeAdded([
    'username' => ['type' => 'input', 'input_type' => 'text', 'label' => 'Username'],
    'role' => ['type' => 'select', 'label' => 'Role', 'options' => $roles, 'value_field' => 'id', 'label_field' => 'name']
]);
```

Set Columns to Edit

```php
$dataTable->columnsToBeEdited([
    'username' => ['type' => 'input', 'input_type' => 'text'],
    'role' => ['type' => 'select', 'options' => $roles, 'value_field' => 'id', 'label_field' => 'name']
]);
```

## Set Add Permission

It takes a boolean value

```php
$dataTable->setAddPermission(true);
```

## Set Actions Button(Add,Edit,Delete)

By default All Buttons are enabled

```php
use App\Types\Buttons;

$dataTable->setButtonsViewable([Buttons::VIEW,Buttons::EDIT]);
```
## Add Action using Input
```php
$dataTable->setAddAction("Yooo")
```

## Edit Action Using Input
```php
$dataTable->setEditAction("Yooo")
```

## Delete Action
```php
$dataTable->setDeleteAction("Yooo")
```

## SweetAlert Delete Title

```php
$dataTable->setDeleteTitle("Delete")
```

## SweetAlert Delete Message

```php
 ->setDeleteMessage("Are you sure you want to delete this item?")
```

## Set BootStrap Version

By Default It uses BootStrap 5

```php
use App\Types\Buttons;

$dataTable->setBootStrapVersion(BootStrap::V3)
```

## Set SweetAlert Vision

By default it Uses SweetAlert 2

```php
use App\Types\SweetAlert;

 $dataTable->setSweetAlertVersion(SweetAlert::V2)
```

## set Datatable Buttons

```php
$dataTable->setDataTableButtons([
    'copy' => true,
    'csv' => ['title' => 'Exported CSV'],
    'excel' => ['title' => 'Excel Export'],
    'pdf' => [
        'title' => 'PDF Export',
        'orientation' => 'landscape',
        'pageSize' => 'A4',
    ],
    'print' => ['title' => 'Printable View'],
    'colvis' => ['text' => 'Toggle Columns'],
]);
```

## Select Fields and JOINs

```php
$dataTable->valuesToSelect([
    'e.id',
    'e.username',
    'r.name AS role_name'
])->tablesToJoin([
    ['table' => 'e.role', 'alias' => 'r', 'on' => 'e.role = r.id']
]);
```

## Values to Render

If there are no Values to Render, it uses the Values to Select as a Fallback to render the items

```php
 ->valuesToRender([
        "name",
        "price",
        "category_name"
    ])
```

### Edit Button Conditions

You can decide to not specify the group operator which by default it will use AND operator as a fallback

```php
    ->setEditButtonConditions([
        'conditions' => [
            ['field' => 'name', 'operator' => '=', 'value' => 'rrrooo'],
            ['field' => 'price', 'operator' => '=', 'value' => '566'],
        ],
    ])
```

## Specifying Group Operator

```php
    ->setEditButtonConditions([
        'conditions' => [
            ['field' => 'name', 'operator' => '=', 'value' => 'rrrooo'],
            ['field' => 'price', 'operator' => '=', 'value' => '566'],
        ],
        'group_operator' => 'OR',
    ])
```

### How to Set Conditions:

## Case 1: Simple OR condition (Conditions connected by OR):

```php

->setEditButtonConditions([
'conditions' => [
['field' => 'name', 'operator' => '=', 'value' => 'rrrooo'],
['field' => 'price', 'operator' => '=', 'value' => '566'],
],
'group_operator' => 'OR',
])
```

This will return true if either name is equal to 'rrrooo' OR price is equal to 566.

## Case 2: Simple AND condition (Conditions connected by AND):

```php
->setEditButtonConditions([
'conditions' => [
['field' => 'name', 'operator' => '=', 'value' => 'rrrooo'],
['field' => 'price', 'operator' => '=', 'value' => '566'],
],
'group_operator' => 'AND',
])
```

This will return true if both name is equal to 'rrrooo' AND price is equal to 566.

## Case 3: Mixed AND and OR (More complex logic with groups):

```php
->setEditButtonConditions([
'groups' => [
[
'conditions' => [
['field' => 'name', 'operator' => '=', 'value' => 'rrrooo'],
['field' => 'price', 'operator' => '=', 'value' => '566'],
],
'group_operator' => 'AND', // Conditions within this group are ANDed
],
[
'conditions' => [
['field' => 'stock', 'operator' => '>', 'value' => '100'],
['field' => 'category', 'operator' => '=', 'value' => 'Electronics'],
],
'group_operator' => 'OR', // Conditions within this group are ORed
],
],
'group_operator' => 'AND', // Groups themselves are ANDed together
])
```

This will evaluate like:

(name = 'rrrooo' AND price = '566')

OR

(stock > 100 OR category = 'Electronics')

The overall result will be true if either the AND group (name and price) is true AND the OR group (stock or category) is
true.

### Edit Callback Function

Instead of using setEditButtonCondition You can opt to use setEditButtonConditionCallback to provide your own callback

```php
   ->setEditButtonConditionCallback(function ($row) {
        return $row['price'] > 500;
    })
```

### Delete Button Conditions

You can decide to not specify the group operator which by default it will use AND operator as a fallback

```php
    ->setDeleteButtonConditions([
        'conditions' => [
            ['field' => 'name', 'operator' => '=', 'value' => 'rrrooo'],
            ['field' => 'price', 'operator' => '=', 'value' => '566'],
        ],
    ])
```

## Specifying Group Operator

```php
    ->setDeleteButtonConditions([
        'conditions' => [
            ['field' => 'name', 'operator' => '=', 'value' => 'rrrooo'],
            ['field' => 'price', 'operator' => '=', 'value' => '566'],
        ],
        'group_operator' => 'OR',
    ])
```

### How to Set Conditions:

## Case 1: Simple OR condition (Conditions connected by OR):

```php

->setDeleteButtonConditions([
'conditions' => [
['field' => 'name', 'operator' => '=', 'value' => 'rrrooo'],
['field' => 'price', 'operator' => '=', 'value' => '566'],
],
'group_operator' => 'OR',
])
```

This will return true if either name is equal to 'rrrooo' OR price is equal to 566.

## Case 2: Simple AND condition (Conditions connected by AND):

```php
->setDeleteButtonConditions([
'conditions' => [
['field' => 'name', 'operator' => '=', 'value' => 'rrrooo'],
['field' => 'price', 'operator' => '=', 'value' => '566'],
],
'group_operator' => 'AND',
])
```

This will return true if both name is equal to 'rrrooo' AND price is equal to 566.

## Case 3: Mixed AND and OR (More complex logic with groups):

```php
->setDeleteButtonConditions([
'groups' => [
[
'conditions' => [
['field' => 'name', 'operator' => '=', 'value' => 'rrrooo'],
['field' => 'price', 'operator' => '=', 'value' => '566'],
],
'group_operator' => 'AND', // Conditions within this group are ANDed
],
[
'conditions' => [
['field' => 'stock', 'operator' => '>', 'value' => '100'],
['field' => 'category', 'operator' => '=', 'value' => 'Electronics'],
],
'group_operator' => 'OR', // Conditions within this group are ORed
],
],
'group_operator' => 'AND', // Groups themselves are ANDed together
])
```

This will evaluate like:

(name = 'rrrooo' AND price = '566')

OR

(stock > 100 OR category = 'Electronics')

The overall result will be true if either the AND group (name and price) is true AND the OR group (stock or category) is
true.

### Delete Callback Function

Instead of using setEditButtonCondition You can opt to use setEditButtonConditionCallback to provide your own callback

```php
   ->setDeleteButtonConditionCallback(function ($row) {
        return $row['price'] > 500;
    })
```

## Custom Add Form

```php
->setCustomAddFormRenderer(function () {
        echo '<div class="mb-3"><label>Custom Field</label><input name="name" class="form-control" /></div>';
    })
```

### Custom Add Form Header/Title

```php
->setCustomAddFormHeader("Add New Category")
```

### Custom Edit Form

THe id Of Inputs Should have the datatable id passed
eg dataTable_editModalprice

Where dataTable is the id of datatable

```php
->setCustomEditFormRenderer(function () {
    echo '<div class="mb-3"><label>Custom Field</label><input id="dataTable_editModalprice" name="name" class="form-control" /></div>';
})
```

### Custom Edit Form With Select

```php
->setCustomEditFormRenderer(function () use ($categories) {
    echo '<div class="mb-3">
        <label for="dataTable_editModalname">Product Name</label>
        <input id="dataTable_editModalname" name="name" class="form-control" />
      </div>';

    echo '<div class="mb-3">
        <label for="dataTable_editModalprice">Price ($)</label>
        <input id="dataTable_editModalprice" name="price"  class="form-control" />
      </div>';

    echo '<div class="mb-3">
        <label for="dataTable_editModalcategory_id">Category</label>
        <select id="dataTable_editModalcategory_id" name="category_id" class="form-control">
            <option value="">-- Select Category --</option>';
    foreach ($categories as $cat) {
        echo '<option value="' . htmlspecialchars($cat['id']) . '">' . htmlspecialchars($cat['name']) . '</option>';
    }
    echo '  </select>
      </div>';
})
```

### Custom Edit Form Header/Title

```php
->setCustomEditFormHeader("Edit Category")
```

### Dialog Size

## Add Dialog Size

```php
->setAddDialogSize("modal-fullscreen")
```

## Edit Dialog Size

```php
->setEditDialogSize("modal-fullscreen")
```

## Actions button Mode

# DropDown

```php 
 ->setActionsButtonMode(ActionsButtonMode::DROPDOWN)
```

# Default

```php
->setActionsButtonMode(ActionsButtonMode::DEFAULT)
```

## View Source Modes

# Link

```php 
->setViewSource(ViewSource::LINK)
```

# Modal

```php 
->setViewSource(ViewSource::MODAL)
```

## Custom View Form Header

```php 
 ->setCustomViewFormHeader("Image")
```

## Values To Show On View Modal

```php 
  ->setValuesToShowonModal([ "name" => [
        "type" => "input",
        "input_type" => "text",
        "label" => "Name",
    ],])
```

## View Dialog Size

```php
->setViewDialogSize("modal-fullscreen")
```

## Enable Search

```php
$dataTable->searchable([
    'e.username' => ['type' => 'input', 'label' => 'Username', 'column' => 'username'],
    'r.id' => ['type' => 'select', 'label' => 'Role', 'column' => 'id', 'options' => $roles, 'value_field' => 'id', 'label_field' => 'name']
]);
```

## Custom Columns Before Actions Columns

```php
 ->addColumnBeforeActions('Status Icon', fn($row) => $row['active'] ? '🟢' : '🔴')
```

## Custom Columns Before Actions Columns Without Being Escaped

```php
->addColumnBeforeActions('Role', fn($row) => "<b>{$row['role']}</b>", raw: true)
```

## Custom Columns Before Actions Columns With Callback Visibility

```php
->addColumnBeforeActions(
    'Secret',
    fn($row) => '🔒 Secret!',
    raw: true,
    visibleWhen: fn($row) => $row['price'] > 1 && $row['name']==='RRR'
)
```

## Custom Columns Before Actions Columns With Boolean Visibility

```php
->addColumnAfterActions(
    'Secret Info',
    fn($row) => '🔒 Secret!',
    raw: true,
    visibleWhen: fn($row) => true
)
```

## Custom Columns After Actions Columns

```php
 ->addColumnAfterActions('Status Icon', fn($row) => $row['active'] ? '🟢' : '🔴')
```

## Custom Columns After Actions Columns Without Being Escaped

```php
->addColumnAfterActions('Role', fn($row) => "<b>{$row['role']}</b>", raw: true)
```

## Custom Columns After Actions Columns With Callback Visibility

```php
->addColumnAfterActions(
    'Secret',
    fn($row) => '🔒 Secret!',
    raw: true,
    visibleWhen: fn($row) => $row['price'] > 1 && $row['name']==='RRR'
)
```

## Custom Columns After Actions Columns With Boolean Visibility

```php
->addColumnAfterActions(
    'Secret Info',
    fn($row) => '🔒 Secret!',
    raw: true,
    visibleWhen: fn($row) => true
)
```

## Data Transformation

```php
->setRowDataTransformer(function($row) {
    if (!empty($row['id'])) {
        $row['name'] = strtoupper($row['name']);
        $row['role'] = strtoupper($row['name']);
    }

    if($row['price']>600){
        $row['active'] =true;
    }
    $row['total_price'] = $row['price'] +1;
    return $row;
})
```

## Enable Pagination

```php
$dataTable->enablePagination(10, ['username', 'role']);
```

## 🖥 Rendering the Table

```php
$dataTable->renderDataTable($_GET['page'] ?? 1);
```

## 🧼 Handling Form Submissions

```php
    $dataTable->handleRequest($_POST ?? []);
```

### ✅ Example a Simple Workflow Without Joins

Columns to add and Columns to Edit support using both associative and indexed array

```php

$dataDisplay = DataDisplay::create($entityManager, User::class);

$dataDisplay
    ->valuesToSelect(["e.id", "e.name"])
    ->columnToBeAdded([
     "name"
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ]);

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();

```

### ✅ Example a Simple Workflow With Add Permission

```php
$dataDisplay = DataDisplay::create($entityManager, User::class);

$dataDisplay
    ->valuesToSelect(["e.id", "e.name"])
    ->columnToBeAdded([
     "name"
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->setAddPermission(true)
    ->setDataTableButtons([
        'copy' => true,
        'csv' => true,
        'excel' => ['title' => 'Excel Export'],
        'pdf' => [
            'title' => 'PDF Export',
            'orientation' => 'landscape',
            'pageSize' => 'A4',
        ],
        'print' => ['title' => 'Printable View'],
        'colvis' => ['text' => 'Toggle Columns'],
    ]);

```

### ✅ Example a Simple Workflow With Datatable Buttons

```php
$dataDisplay = DataDisplay::create($entityManager, User::class);

$dataDisplay
    ->valuesToSelect(["e.id", "e.name"])
    ->columnToBeAdded([
     "name"
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->setDataTableButtons([
        'copy' => true,
        'csv' => true,
        'excel' => ['title' => 'Excel Export'],
        'pdf' => [
            'title' => 'PDF Export',
            'orientation' => 'landscape',
            'pageSize' => 'A4',
        ],
        'print' => ['title' => 'Printable View'],
        'colvis' => ['text' => 'Toggle Columns'],
    ]);
```

### ✅ Example a Simple Workflow With Custom Add and Edit Form

```php
$dataDisplay = DataDisplay::create($entityManager, User::class);

$dataDisplay
    ->valuesToSelect(["e.id", "e.name"])
    ->columnToBeAdded([
     "name"
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->setAddPermission(true)
    ->setCustomAddFormRenderer(function () {
        echo '<div class="mb-3"><label>Custom Field</label><input name="name" class="form-control" /></div>';
    })
    ->setCustomEditFormRenderer(function () {
        echo '<div class="mb-3"><label>Custom Field</label><input id="name" name="name" class="form-control" /></div>';
    })
    ->setDataTableButtons([
        'copy' => true,
        'csv' => true,
        'excel' => ['title' => 'Excel Export'],
        'pdf' => [
            'title' => 'PDF Export',
            'orientation' => 'landscape',
            'pageSize' => 'A4',
        ],
        'print' => ['title' => 'Printable View'],
        'colvis' => ['text' => 'Toggle Columns'],
    ]);

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();
```

### ✅ Example a Simple Workflow With Joins and Search Functionality

```php
$categories = Category::getAllCategories($entityManager) ?? [];

$dataDisplay = DataDisplay::create($entityManager, Product::class);

$dataDisplay
    ->valuesToSelect(["e.id", "e.name", "e.price", "c.name AS category_name"])
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category",
            "options" => $categories,
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => "Price",
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category",
            "options" => $categories,
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->enablePagination(10, ["e.name", "e.id"])
    ->searchable([
        "category_id" => [
            "type" => "select",
            "label" => "Category",
            "operator" => "=",
            "table" => "e",
            "column" => "category_id",
            "options" => $categories,
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])

    ->setAddButtonLabel("Create New Product")
    ->setTableId("dataTable")
    ->tablesToJoin([
        [
            "table" => "Category::class",
            "alias" => "c",
            "on" => "e.category_id = c.id",
        ],
    ]);

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();
```

### ✅ Example a Simple Workflow With Custom Add and Edit Form

```php
$categories = Category::getAllCategories($entityManager) ?? [];

$dataDisplay = DataDisplay::create($entityManager, Product::class);

$dataDisplay
    ->valuesToSelect(["e.id", "e.name", "e.price","e.category_id", "c.name AS category_name"])
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category",
            "options" => $categories,
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => "Price",
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category 1",
            "options" => $categories,
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->valuesToRender([
        "id",
        "name",
        "price",
        "category_name"
    ])
    ->setCustomAddFormHeader("Add New Category")
    ->setCustomEditFormHeader("Edit Category")
    ->setCustomEditFormRenderer(function () use ($categories) {
        echo '<div class="mb-3">
            <label for="dataTable_editModalname">Product Name</label>
            <input id="dataTable_editModalname" name="name" class="form-control" />
          </div>';

        echo '<div class="mb-3">
            <label for="dataTable_editModalprice">Price ($)</label>
            <input id="dataTable_editModalprice" name="price"  class="form-control" />
          </div>';

        echo '<div class="mb-3">
            <label for="dataTable_editModalcategory_id">Category</label>
            <select id="dataTable_editModalcategory_id" name="category_id" class="form-control">
                <option value="">-- Select Category --</option>';
        foreach ($categories as $cat) {
            echo '<option value="' . htmlspecialchars($cat['id']) . '">' . htmlspecialchars($cat['name']) . '</option>';
        }
        echo '  </select>
          </div>';
    })

    ->setEditButtonConditionCallback(function ($row) {
        return $row['price'] > 100;
    })

    ->searchable([
        "category" => [
            "type" => "select",
            "label" => "Category",
            "operator" => "=",
            "table" => "e",
            "column" => "category_id",
            "options" => $categories,
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])

    ->setAddButtonLabel("Create New Product")
    ->setTableId("dataTable")
    ->tablesToJoin([
        [
            "table" => Category::class,
            "alias" => "c",
            "on" => "e.category_id = c.id",
        ],
    ])
    ->setDataTableButtons([
        'copy' => true,
        'csv' => ['title' => 'Exported CSV'],
        'excel' => ['title' => 'Excel Export'],
        'pdf' => [
            'title' => 'PDF Export',
            'orientation' => 'landscape',
            'pageSize' => 'A4',
        ],
        'print' => ['title' => 'Printable View'],
        'colvis' => ['text' => 'Toggle Columns'],
    ]);

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();
```

### ✅ Example a Simple Workflow With Data Transformation,Joins and Custom Columns

```php
$categories = Category::getAllCategories($entityManager) ?? [];

$dataDisplay = DataDisplay::create($entityManager, Product::class);

$dataDisplay
    ->valuesToSelect(["e.id", "e.name", "e.price","e.category_id", "c.name AS category_name"])
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category",
            "options" => $categories,
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => "Price",
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category 1",
            "options" => $categories,
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->valuesToRender([
        "id",
        "name",
        "price",
        "category_name"
    ])
    ->setRowDataTransformer(function($row) {
        if (!empty($row['id'])) {
            $row['name'] = strtoupper($row['name']);
            $row['role'] = strtoupper($row['name']);
        }

        if($row['price']>600){
            $row['active'] =true;
        }
        $row['total_price'] = $row['price'] +1;
        return $row;
    })
    ->addColumnAfterActions('Status Icon', fn($row) => $row['active'] ? '🟢' : '🔴')
    ->addColumnBeforeActions('Role', fn($row) => "<b>{$row['role']}</b>", raw: true)
    ->addColumnAfterActions(
        'Secret Info',
        fn($row) => '🔒 Secret!',
        raw: true,
        visibleWhen: fn($row) => true
    )
    ->addColumnAfterActions(
        'Secret',
        fn($row) => '🔒 Secret!',
        raw: true,
        visibleWhen: fn($row) => $row['price'] > 1 && $row['name']==='RRR'
    )
    ->tablesToJoin([
        [
            "table" => Category::class,
            "alias" => "c",
            "on" => "e.category_id = c.id",
        ],
    ]);
```

### ✅ Example a Simple Workflow With Classes For Data Fetching

```php
$dataDisplay = DataDisplay::create(null, Category::class,DataSourceType::CLASSES);

$dataDisplay
   ->setClassFetchDataFunction("all")
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->valuesToRender(["id", "name"])
    ->searchable([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ;

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();
```

### ✅ Example a Simple Workflow With Report Mode

```php
$dataDisplay = DataDisplay::create(null, Category::class,DataSourceType::CLASSES);

$dataDisplay
   ->setClassFetchDataFunction("all")
    ->setMode(DataDisplayModes::REPORT)
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->valuesToRender(["id", "name"])
    ->searchable([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->setCustomAddAction("/test")
    ->setCustomEditAction("/admin")
    ;

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();
```

### ✅ Example a Simple Workflow With Dialog Size

```php
$dataDisplay = DataDisplay::create(null, Category::class,DataSourceType::CLASSES);

$dataDisplay
   ->setClassFetchDataFunction("all")
   ->setAddDialogSize("modal-fullscreen")
   ->setEditDialogSize("modal-fullscreen")
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->valuesToRender(["id", "name"])
    ->searchable([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->setCustomAddAction("/test")
    ->setCustomEditAction("/admin")
    ;

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();
```

### ✅ Example a Simple Workflow With Action Buttons in Dropdown Mode

```php 
$dataDisplay = DataDisplay::create(null, Category::class,DataSourceType::CLASSES);

$dataDisplay
    ->setClassFetchDataFunction("all")
    ->setAddDialogSize("modal-fullscreen")
    ->setEditDialogSize("modal-fullscreen")
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category",
            "options" => [],
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->setActionsButtonMode(ActionsButtonMode::DROPDOWN)
    ->valuesToRender(["id", "name"])
    ->setCustomAddAction("/test")
    ->setCustomEditAction("/admin");
```

### ✅ Example a Simple Workflow With View Source(Modal) and Action Button Modes(DropDown)

```php 
$dataDisplay = DataDisplay::create(null, Category::class,DataSourceType::CLASSES);

$dataDisplay
    ->setClassFetchDataFunction("all")
    ->setAddDialogSize("modal-fullscreen")
    ->setEditDialogSize("modal-fullscreen")
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category",
            "options" => [],
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->setActionsButtonMode(ActionsButtonMode::DROPDOWN)
    ->setViewSource(ViewSource::MODAL)
    ->setViewDialogSize("modal-fullscreen")
    ->setCustomViewFormHeader("Image")
    ->setValuesToShowonModal([ "name" => [
        "type" => "input",
        "input_type" => "text",
        "label" => "Name",
    ],])
    ->valuesToRender(["id", "name"])
    ->searchable([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
    ])
    ->setCustomAddAction("/test")
    ->setCustomEditAction("/admin");

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();
```

### ✅ Example a Simple Workflow With View Source(Link) and Action Button Modes(Default)

```php 
$dataDisplay = DataDisplay::create(null, Category::class,DataSourceType::CLASSES);

$dataDisplay
    ->setClassFetchDataFunction("all")
    ->setAddDialogSize("modal-fullscreen")
    ->setEditDialogSize("modal-fullscreen")
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category",
            "options" => [],
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
     ->setViewLink("/test=")
    ->setViewDialogSize("modal-fullscreen")
    ->setCustomViewFormHeader("Image")
    ->setValuesToShowonModal([ "name" => [
        "type" => "input",
        "input_type" => "text",
        "label" => "Name",
    ],])
    ->valuesToRender(["id", "name"])
    ->searchable([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
    ])
    ->setCustomAddAction("/test")
    ->setCustomEditAction("/admin");

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();
```

### ✅ Example a Simple Workflow With Delete Conditions

```php 
$dataDisplay = DataDisplay::create(null, Category::class,DataSourceType::CLASSES);

$dataDisplay
    ->setClassFetchDataFunction("all")
    ->setAddDialogSize("modal-fullscreen")
    ->setEditDialogSize("modal-fullscreen")
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category",
            "options" => [],
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])
    ->setViewLink("/test/")
    ->setViewDialogSize("modal-fullscreen")
    ->setCustomViewFormHeader("Image")
    ->setValuesToShowonModal([ "name" => [
        "type" => "input",
        "input_type" => "text",
        "label" => "Name",
    ],])

     ->setDeleteButtonConditions([
        'conditions' => [
            ['field' => 'name', 'operator' => '=', 'value' => 'Books'],
        ],
    ])
    ->valuesToRender(["id", "name"])
    ->searchable([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
    ])
    ->setCustomAddAction("/test")
    ->setCustomEditAction("/admin");

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();
```

### ✅ Example a Simple Workflow With Delete Callback

```php
$dataDisplay = DataDisplay::create(null, Category::class,DataSourceType::CLASSES);

$dataDisplay
    ->setClassFetchDataFunction("all")
    ->setAddDialogSize("modal-fullscreen")
    ->setEditDialogSize("modal-fullscreen")
    ->columnToBeAdded([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
        "category_id" => [
            "type" => "select",
            "label" => "Category",
            "options" => [],
            "value_field" => "id",
            "label_field" => "name",
        ],
    ])
    ->columnsToBeEdited([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Name",
        ],
    ])

    ->setViewLink("/test/")
    ->setViewDialogSize("modal-fullscreen")
    ->setCustomViewFormHeader("Image")
    ->setValuesToShowonModal([ "name" => [
        "type" => "input",
        "input_type" => "text",
        "label" => "Name",
    ],])

    ->setDeleteButtonConditionCallback(function ($row) {
        return $row["name"] !== 'Books';
    })
    ->valuesToRender(["id", "name"])
    ->searchable([
        "name" => [
            "type" => "input",
            "input_type" => "text",
            "label" => "Product Name",
        ],
        "price" => [
            "type" => "input",
            "input_type" => "number",
            "label" => 'Price ($)',
        ],
    ])
    ->setCustomAddAction("/test")
    ->setCustomEditAction("/admin");

$dataDisplay->handleRequest($_POST ?? []);
$dataDisplay->renderDataTable();
```