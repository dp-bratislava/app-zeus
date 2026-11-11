# WIP - AI generated needs checkup

# Task Batch Configuration

Task batch configuration defines how tasks are generated and grouped within a batch.

Two configuration modes are supported:

- **Uniform** – applies a shared configuration to multiple subjects.
- **Diverse** – defines each task independently, allowing different subjects, work items, operations, and notes.

Every configuration must specify a `mode` and a `version`.

## Configuration Modes

### 1. Uniform Mode

Uniform mode is intended for batches where multiple subjects share the same task configuration.

#### Example

```json
{
  "mode": "uniform",
  "version": "1",
  "subjects": {
    "vehicle": [680, 701]
  },
  "contract_ids": [627, 779],
  "task_item_groups": {
    "45": [2900104]
  }
}
```

#### Properties

| Property | Type | Required | Description |
|---|---|---|---|
| `mode` | string | Yes | Must be `uniform`. |
| `version` | string | Yes | Configuration schema version. |
| `subjects` | object | Yes | Subjects grouped by subject type. |
| `contract_ids` | array | Yes | Contract IDs shared across the batch. |
| `task_item_groups` | object | Yes | Task item IDs grouped by task item group ID. |

#### Subjects

The `subjects` object contains subject types as keys and arrays of subject IDs as values.

```json
{
  "subjects": {
    "vehicle": [680, 701]
  }
}
```

#### Task Item Groups

The `task_item_groups` object maps task item group IDs to arrays of task item IDs.

```json
{
  "task_item_groups": {
    "45": [2900104]
  }
}
```

### 2. Diverse Mode

Diverse mode allows each task in the batch to have its own configuration.

Tasks can have different subjects, notes, work definitions, operations, and task item groups.

#### Example

```json
{
  "mode": "diverse",
  "version": "1",
  "tasks": [
    {
      "note": "jj",
      "subject_id": 2,
      "subject_type": "vehicle",
      "task_items": [
        {
          "work": [
            {
              "quantity": 1,
              "contract_id": 4102
            }
          ],
          "operations": [290019279],
          "task_item_group_id": 445
        }
      ]
    },
    {
      "note": "",
      "subject_id": 5,
      "subject_type": "vehicle",
      "task_items": [
        {
          "work": [
            {
              "quantity": 2,
              "contract_id": 4102
            }
          ],
          "operations": [290019274],
          "task_item_group_id": 434
        }
      ]
    }
  ]
}
```

#### Root Properties

| Property | Type | Required | Description |
|---|---|---|---|
| `mode` | string | Yes | Must be `diverse`. |
| `version` | string | Yes | Configuration schema version. |
| `tasks` | array | Yes | Individual task definitions. |

#### Task Properties

| Property | Type | Required | Description |
|---|---|---|---|
| `note` | string | Yes | Task note. Can be empty. |
| `subject_id` | integer | Yes | ID of the subject associated with the task. |
| `subject_type` | string | Yes | Type of subject, e.g. `vehicle`. |
| `task_items` | array | Yes | Task item definitions belonging to the task. |

#### Task Item Properties

| Property | Type | Required | Description |
|---|---|---|---|
| `work` | array | Yes | Work definitions associated with the task item. |
| `operations` | array | Yes | Operation IDs associated with the task item. |
| `task_item_group_id` | integer | Yes | Task item group identifier. |

#### Work Properties

| Property | Type | Required | Description |
|---|---|---|---|
| `quantity` | number | Yes | Quantity of the specified work. |
| `contract_id` | integer | Yes | Contract associated with the work. |

## Choosing a Mode

| Feature | Uniform | Diverse |
|---|---|---|
| Multiple subjects | Yes | Yes |
| Shared contract IDs | Yes | No |
| Shared task item groups | Yes | No |
| Individual task notes | No | Yes |
| Individual task configuration | No | Yes |
| Different work quantities per task | No | Yes |

**Use uniform mode** when all tasks follow the same structure and share configuration.

**Use diverse mode** when individual tasks require different configurations.

## Versioning

The `version` property identifies the configuration schema version.

Currently supported version:

- `1`

When introducing incompatible configuration changes, introduce a new schema version rather than silently changing the meaning of existing fields.
