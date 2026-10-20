# Task Batch Configuration

## Overview

Defines the configuration used to create a batch of tasks, including the subject, task group, task item groups, and assigned work contracts.

**Schema version:** `1`

## Structure

| Field | Type | Required | Description |
|---|---|---|---|
| `version` | integer | Yes | Configuration schema version |
| `tasks` | array | Yes | Tasks to create in the batch |

### `tasks[]`

| Field | Type | Required | Description |
|---|---|---|---|
| `note` | string | Yes | Optional task note; may be an empty string |
| `subject_id` | integer | Yes | ID of the subject record |
| `subject_type` | string | Yes | Morph-map alias identifying the subject type, e.g. `vehicle` |
| `task_group_id` | integer | Yes | ID of the task group |
| `task_item_groups` | array | Yes | Task item groups and their assigned work |

### `tasks[].task_item_groups[]`

| Field | Type | Required | Description |
|---|---|---|---|
| `task_item_group_id` | integer | Yes | ID of the task item group |
| `work` | array | Yes | Work assignments associated with this group |

### `tasks[].task_item_groups[].work[]`

| Field | Type | Required | Description |
|---|---|---|---|
| `contract_id` | integer | Yes | ID of the employee contract |
| `quantity` | integer | No | Quantity of work; defaults to `1` if omitted |

## Example

```json
{
  "version": 1,
  "tasks": [
    {
      "note": "",
      "subject_id": 510,
      "subject_type": "vehicle",
      "task_group_id": 9,
      "task_item_groups": [
        {
          "task_item_group_id": 397,
          "work": [
            {
              "contract_id": 1254,
              "quantity": 1
            },
            {
              "contract_id": 1822,
              "quantity": 1
            }
          ]
        }
      ]
    }
  ]
}
```

## Validation Rules

- `version` must match a supported configuration version.
- `subject_type` must be a registered Laravel morph-map alias.
- `subject_id` must identify an existing subject of the specified type.
- `task_group_id` must identify an existing task group.
- Each `task_item_group_id` must identify a valid task item group.
- Each `contract_id` must identify a valid employee contract.
- `quantity` must be a positive integer.
- Work assignments and task item groups must satisfy the application's business rules.

## Compatibility and Versioning

Increment `version` when making a breaking change to the configuration structure or its interpretation.

When loading older configurations, either support their version explicitly or provide a migration to the current schema. Do not silently interpret an old configuration as the newest version.
