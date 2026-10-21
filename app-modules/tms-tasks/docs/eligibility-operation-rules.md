# WIP AI generated needs checkup

# Operation Rules

## Purpose

Operation rules define which operations are available for a given task item group and subject type, based on the subject's attributes.

Rules are stored in the `operation_rules` table. Each rule references a task item group, a subject type, an operation, and a JSON object containing eligibility conditions.

An operation is available when its associated rule's conditions match the subject.

## JSON structure

Example:

```json
{
  "length": {
    "gte": 12,
    "lt": 18
  },
  "vehicle_type": {
    "in": ["T"]
  }
}
```

This rule matches vehicles whose model:

- Has a length greater than or equal to `12`.
- Has a length strictly less than `18`.
- Has a type code equal to `"T"`.

All specified conditions must match. Conditions are combined using logical **AND**.

## Supported conditions

### `length`

Checks the vehicle model's `length` attribute.

Supported operators:

| Operator | Meaning | Example |
|---|---|---|
| `gt` | Greater than | `"gt": 12` |
| `gte` | Greater than or equal to | `"gte": 12` |
| `lt` | Less than | `"lt": 18` |
| `lte` | Less than or equal to | `"lte": 18` |

Example:

```json
{
  "length": {
    "gte": 12,
    "lt": 18
  }
}
```

The condition matches lengths in the interval `[12, 18)`.

Multiple operators can be combined to define a range. If an operator is omitted, that bound is not checked.

### `vehicle_type`

Checks the vehicle model's type code.

Supported operator:

| Operator | Meaning |
|---|---|
| `in` | The type code must be one of the specified values |

Example:

```json
{
  "vehicle_type": {
    "in": ["T", "B"]
  }
}
```

The condition matches vehicles whose model type code is either `"T"` or `"B"`.

Type codes are compared strictly, so values must have the correct type and casing.

## Combining conditions

Conditions can be combined in one JSON object:

```json
{
  "length": {
    "gte": 12,
    "lt": 18
  },
  "vehicle_type": {
    "in": ["T"]
  }
}
```

A vehicle matches only if **both** its length and its type satisfy their respective conditions.

If a condition is omitted, that condition does not restrict eligibility.

An empty conditions object (`{}`) matches any subject supported by the corresponding evaluator, provided the evaluator's own prerequisites are satisfied.

## Rule evaluation

For vehicle subjects, evaluation follows these steps:

1. Confirm the subject is a vehicle.
2. Load the vehicle's model.
3. Reject the subject if it has no associated model.
4. Evaluate all specified conditions.
5. Match the rule only if every condition passes.

Rules are selected by task item group and subject type. Operations from matching rules are returned without duplicate operation IDs.

## Data model

Each operation rule contains:

| Field | Description |
|---|---|
| `task_item_group_id` | Task item group to which the rule applies |
| `subject_type` | Subject type identifier, such as the configured vehicle morph alias |
| `operation_id` | Operation made available when the rule matches |
| `conditions` | JSON object containing eligibility conditions |

The `conditions` column is cast to an array by the Eloquent model.

## Validation recommendations

Before saving a rule:

- Ensure `conditions` is a valid JSON object.
- Ensure condition names and operators are supported by the evaluator.
- Use numeric values for length bounds.
- Use strings for vehicle type codes.
- Ensure `length` bounds are logically consistent, for example, `gte` is not greater than or equal to `lt`.
- Ensure `vehicle_type.in` is an array of valid type codes.

**Important:** The current evaluator does not explicitly reject unknown conditions or operators. Unsupported conditions may be ignored, so validate the JSON before persisting it.

## Extensibility

Additional subject types can have their own evaluators implementing `SubjectRuleEvaluator`. Each evaluator defines which conditions are supported for its subject type.

When introducing a new condition, update the corresponding evaluator and its validation rules, and document the new condition here.
