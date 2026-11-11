# WIP - AI generated needs checkup

# Task Batch Operation Rules

Task batch operation rules define which operations are available for a particular department, task group, task item group, and subject type.

Rules allow operations to be dynamically filtered based on subject properties.

For example:
- A vehicle may be eligible for an operation only if its length meets certain requirements.
- An operation may only be available for specific vehicle types.
- Different departments can configure different operations for the same task item group.

## Overview

Operation eligibility is determined by combining:

1. Department configuration.
2. Task group configuration.
3. Task item group configuration.
4. Subject type.
5. Rule conditions.
6. Subject-specific evaluation logic.

The system uses subject evaluators to determine whether a subject matches a rule's conditions.

## Database Structure

The configuration consists of three main tables.

### 1. Department Task Groups

Table: `tms_task_batch_department_task_group`

Binds a task group to a department and defines the handling mode.

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key. |
| `department_id` | bigint | Department identifier. |
| `task_group_id` | bigint | Task group identifier. |
| `mode` | varchar | Task batch handling mode, e.g. `uniform` or `diverse`. |

A department can have its own configuration for a task group.

### 2. Department Task Group Items

Table: `tms_task_batch_department_task_group_items`

Defines which task item groups are available within a department's task group configuration.

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key. |
| `department_task_group_id` | bigint | Reference to department task group configuration. |
| `task_item_group_id` | bigint | Task item group identifier. |
| `is_scalable` | boolean | Determines the GUI form component type. |
| `label` | varchar | Custom display label for the task item group. |

The combination of `department_task_group_id` and `task_item_group_id` must be unique.

The `label` can differ from the original task item group title and is intended for task batch form presentation.

### 3. Department Operation Rules

Table: `tms_task_batch_department_operation_rules`

Defines operations and their eligibility conditions.

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key. |
| `department_task_group_item_id` | bigint | Reference to department task group item. |
| `operation_id` | bigint | Work Time Fund operation identifier. |
| `subject_type` | varchar | Subject morph type, e.g. `vehicle`, `building`. |
| `conditions` | JSON | Conditions that must be satisfied by the subject. |

Rules reference both the department task group item and the operation.

Deleting a department task group item or operation also deletes its associated rules.

## Rule Conditions

Conditions are stored as JSON in the `conditions` column.

The supported condition structure depends on the subject type and its registered evaluator.

### Vehicle Conditions

Vehicle conditions are evaluated against the vehicle's model properties.

Currently supported conditions:

#### Length

Supports comparison operators:

| Operator | Description |
|---|---|
| `gte` | Greater than or equal to. |
| `gt` | Greater than. |
| `lte` | Less than or equal to. |
| `lt` | Less than. |

Example:

```json
{
  "length": {
    "gte": 5,
    "lt": 12
  }
}
```

This condition matches vehicles whose model length is greater than or equal to 5 and less than 12.

Multiple comparison operators can be combined.

#### Vehicle Type

The `vehicle_type` condition restricts eligibility to specific vehicle type codes.

Example:

```json
{
  "vehicle_type": {
    "in": ["TRUCK", "VAN"]
  }
}
```

The vehicle's model type code must match one of the configured values.

#### Combined Conditions

Multiple conditions can be combined in one JSON object.

Example:

```json
{
  "length": {
    "gte": 5,
    "lt": 12
  },
  "vehicle_type": {
    "in": ["TRUCK", "VAN"]
  }
}
```

All configured conditions must match for the rule to be considered eligible.

Conditions that are not configured are not evaluated.

## Operation Eligibility Resolution

Available operations are resolved through the `AvailableOperations` service.

```php
AvailableOperations::for(
    int $departmentId,
    int $taskItemGroupId,
    Model $subject,
): Collection
```

The resolution process:

1. Determine the subject morph type using `getMorphClass()`.
2. Resolve the registered evaluator for that subject type.
3. Retrieve rules matching:
   - Department ID.
   - Task item group ID.
   - Subject type.
4. Evaluate each rule against the supplied subject.
5. Keep only matching rules.
6. Return the associated operations.
7. Remove duplicate operations.

The result is an Eloquent collection of eligible Work Time Fund operations.

Only operations with matching rules and conditions are returned.

## Subject Evaluators

Each supported subject type must have a registered evaluator implementing the `SubjectRuleEvaluator` interface.

```php
interface SubjectRuleEvaluator
{
    public function supports(Model $subject): bool;

    public function matches(
        Model $subject,
        array $conditions,
    ): bool;
}
```

### Evaluator Methods

| Method | Description |
|---|---|
| `supports()` | Determines whether the evaluator supports the supplied subject model. |
| `matches()` | Determines whether the subject satisfies the configured conditions. |

### Evaluator Registry

The `SubjectEvaluatorRegistry` manages evaluator registrations.

Register an evaluator using:

```php
$registry->register(
    'vehicle',
    VehicleRuleEvaluator::class,
);
```

The registry:

- Maps subject types to evaluator classes.
- Ensures registered classes implement `SubjectRuleEvaluator`.
- Prevents duplicate registrations.
- Resolves evaluators through the Laravel service container.
- Throws an exception if an evaluator is not registered.

### VehicleRuleEvaluator

The `VehicleRuleEvaluator` handles vehicle eligibility.

It currently:

1. Checks that the supplied model is a `Vehicle`.
2. Retrieves the vehicle's model.
3. Rejects vehicles without an associated model.
4. Evaluates configured length conditions.
5. Evaluates configured vehicle type conditions.
6. Returns whether all configured conditions match.

## Adding Support for a New Subject Type

To support another subject type, implement a new evaluator.

For example, a building evaluator:

```php
final class BuildingRuleEvaluator implements SubjectRuleEvaluator
{
    public function supports(Model $subject): bool
    {
        return $subject instanceof Building;
    }

    public function matches(
        Model $subject,
        array $conditions,
    ): bool {
        if (! $this->supports($subject)) {
            return false;
        }

        // Implement building-specific conditions.

        return true;
    }
}
```

Register the evaluator:

```php
$registry->register(
    'building',
    BuildingRuleEvaluator::class,
);
```

The `subject_type` stored in operation rules must correspond to the subject's morph class returned by `getMorphClass()`.

## Database Views

The system provides read-only views for retrieving the combined configuration.

### Department Task Groups

`vw_tms_task_batch_department_task_groups`

Provides department task group configuration, including the task group label and handling mode.

### Department Task Item Groups

`vw_tms_task_batch_department_task_item_groups`

Provides available task item groups for a department, including:

- Department ID.
- Task group ID.
- Task item group ID.
- Display label.
- Scalability flag.

### Department Operation Rules

`vw_tms_task_batch_department_operation_rules`

Provides operation rules with their associated department, task group, and task item group information.

The `DepartmentOperationRuleView` Eloquent model represents this view and is read-only.

## Important Notes

- Operation rules are department-specific.
- Rules are associated with task item groups through department task group items.
- Rules are filtered by subject morph type.
- All configured conditions must match.
- An operation can be associated with multiple rules.
- Duplicate operations are removed from the final result.
- Every supported subject type requires a registered evaluator.
- New subject types can introduce their own condition structures without changing the operation resolution service.
