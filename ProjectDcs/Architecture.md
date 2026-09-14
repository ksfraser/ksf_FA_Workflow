# Architecture - ksf_FA_Workflow

## Overview

This document describes the technical architecture for the FA_Workflow module, including the system design, database schema, component structure, and integration patterns.

---

## 1. System Architecture

### 1.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Presentation Layer                      │
│  ┌──────────────┐  ┌──────────────┐                      │
│  │ Workflows  │  │  Actions   │                      │
│  │   Page    │  │   Page    │                      │
│  └─────┬──────┘  └─────┬──────┘                      │
│        │               │                             │
├────────┼───────────────┼──────────────────────────────┤
│        │    Service Layer                          │
│        ▼                                         │
│  ┌────────────────────────────────────────┐      │
│  │              wf_db.inc                  │      │
│  │  - add_trigger(), get_triggers()         │      │
│  │  - add_action(), get_actions()           │      │
│  │  - evaluate_trigger(), execute_action()  │      │
│  │  - run_workflows()                    │      │
│  └────────────────────────────────────────┘      │
├──────────────────────────────────────────────────────┤
│                    Business Layer                   │
│  ┌────────────────────────────────────────┐      │
│  │     Workflow Engine Execution             │      │
│  │  - Trigger Evaluation                 │      │
│  │  - Condition Matching                 │      │
│  │  - Action Execution                 │      │
│  └────────────────────────────────────────┘      │
├──────────────────────────────────────────────────────┤
│                    Data Layer                       │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐     │
│  │ Triggers │  │ Actions  │  │   Log   │     │
│  │  Table   │  │  Table   │  │  Table  │     │
│  └──────────┘  └──────────┘  └──────────┘     │
├──────────────────────────────────────────────────────┤
│                  Integration Layer                   │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐     │
│  │FA Core  │  │  Entity  │  │ Events   │     │
│  │(hooks) │  │ Services │  │(PSR-14)  │     │
│  └──────────┘  └──────────┘  └──────────┘     │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 Module Structure

```
ksf_FA_Workflow/
├── hooks.php            # Module installation hooks
├── includes/
│   └── wf_db.inc     # Database functions
├── pages/
│   ├── workflows.php  # Trigger management UI
│   └── actions.php  # Action management UI
└── ProjectDcs/
    ├── Architecture.md
    ├── Functional Requirements.md
    ├── Test Plan.md
    └── UAT Plan.md
```

---

## 2. Component Design

### 2.1 Core Components

#### wf_db.inc
The database functions file provides all CRUD operations and workflow execution logic.

**Functions**:

| Function | Description |
|----------|-------------|
| `add_trigger()` | Create new trigger |
| `get_trigger()` | Get single trigger |
| `get_triggers()` | List triggers with filters |
| `update_trigger()` | Update trigger |
| `delete_trigger()` | Delete trigger and actions |
| `add_action()` | Create new action |
| `get_actions()` | List actions for trigger |
| `update_action()` | Update action |
| `delete_action()` | Delete action |
| `evaluate_trigger()` | Evaluate trigger condition |
| `execute_action()` | Execute action |
| `run_workflows()` | Execute all matching workflows |
| `log_workflow_execution()` | Log execution result |

### 2.2 Workflow Execution Flow

```
run_workflows(entity_type, entity, oldEntity, context)
    │
    ▼
┌─────────────────────────────────┐
│ 1. Get Active Triggers           │
│    for entity_type              │
└─────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────┐
│ 2. Loop through triggers       │
│    evaluate_trigger()          │
│    - Get field values         │
│    - Apply operator          │
│    - Return boolean          │
└─────────────────────────────────┘
    │
    ▼ (If true)
┌─────────────────────────────────┐
│ 3. Get Actions                │
│    for trigger                │
│    sort by action_order      │
└─────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────┐
│ 4. Loop through actions       │
│    execute_action()          │
│    - Parse action_type       │
│    - Apply config         │
│    - Update entity        │
└─────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────┐
│ 5. Log Execution              │
│    log_workflow_execution()  │
└─────────────────────────────────┘
```

---

## 3. Database Schema

### 3.1 fa_wf_triggers

```sql
CREATE TABLE fa_wf_triggers (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    entity_type VARCHAR(20) NOT NULL,
    field_name VARCHAR(50) NOT NULL,
    operator VARCHAR(20) DEFAULT 'equals',
    field_value VARCHAR(255) DEFAULT NULL,
    trigger_type VARCHAR(20) DEFAULT 'on_save',
    is_active TINYINT(1) DEFAULT 1,
    priority INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_entity (entity_type),
    KEY idx_active (is_active)
);
```

### 3.2 fa_wf_actions

```sql
CREATE TABLE fa_wf_actions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    trigger_id INT(11) NOT NULL,
    name VARCHAR(100) NOT NULL,
    action_type VARCHAR(30) NOT NULL,
    action_config TEXT,
    action_order INT(11) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_trigger (trigger_id)
);
```

### 3.3 fa_wf_workflows

```sql
CREATE TABLE fa_wf_workflows (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    entity_type VARCHAR(20) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_entity (entity_type)
);
```

### 3.4 fa_wf_log

```sql
CREATE TABLE fa_wf_log (
    id INT(11) NOT NULL AUTO_INCREMENT,
    workflow_id INT(11) DEFAULT NULL,
    trigger_id INT(11) DEFAULT NULL,
    action_id INT(11) DEFAULT NULL,
    entity_type VARCHAR(20) DEFAULT NULL,
    entity_id INT(11) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'success',
    error_message TEXT,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_workflow (workflow_id),
    KEY idx_entity (entity_type, entity_id)
);
```

---

## 4. Operator Implementation

### 4.1 Comparison Logic (evaluate_trigger)

```php
function evaluate_trigger($trigger, $entity, $oldEntity): bool
{
    $fieldName = $trigger['field_name'];
    $operator = $trigger['operator'];
    $fieldValue = $trigger['field_value'];
    
    $currentValue = $entity[$fieldName] ?? null;
    $oldValue = $oldEntity[$fieldName] ?? null;
    
    return match ($operator) {
        'equals'        => $currentValue == $fieldValue,
        'not_equals'   => $currentValue != $fieldValue,
        'contains'      => $currentValue !== null && strpos($currentValue, $fieldValue) !== false,
        'is_empty'      => empty($currentValue),
        'is_not_empty'  => !empty($currentValue),
        'changes'       => $currentValue != $oldValue,
        'changes_from'  => $oldValue == $fieldValue && $currentValue != $fieldValue,
        'greater_than'  => is_numeric($currentValue) && $currentValue > $fieldValue,
        'less_than'    => is_numeric($currentValue) && $currentValue < $fieldValue,
        default        => false,
    };
}
```

### 4.2 Action Handlers (execute_action)

```php
function execute_action($action, &$entity, $context = null): bool
{
    $actionType = $action['action_type'];
    $config = $action['action_config'] ?? [];
    
    return match ($actionType) {
        'update_field', 'set_field' => wf_update_field($entity, $config),
        'calculate'               => wf_calculate($entity, $config),
        'assign_to'               => wf_assign_to($entity, $config),
        'trigger_event'          => wf_trigger_event($action, $entity),
        'send_email'              => wf_send_email($config, $entity),
        'add_note'               => wf_add_note($config, $entity),
        default                  => true,
    };
}
```

---

## 5. Integration Patterns

### 5.1 FA Module Integration

The module follows FrontAccounting conventions:

- Uses `TB_PREF` for table prefix
- Uses `db_query()`, `db_fetch_assoc()` for database
- Uses `db_escape()` for SQL injection prevention
- Uses FA hooks system for lifecycle events

### 5.2 Entity Integration

Supported entities map to FA tables:

| entity_type | FA Table |
|------------|----------|
| debtor | debtors_master |
| contact | fa_crm_contacts |
| opportunity | fa_crm_opportunities |
| lead | fa_crm_leads |
| ticket | fa_sv_tickets |

### 5.3 Event Integration

The module can dispatch PSR-14 events:

```php
// Before workflow execution
crm_dispatch_event('workflow.before_execute', [
    'entity_type' => $entity_type,
    'entity_id' => $entity['id'],
]);

// After workflow completes
crm_dispatch_event('workflow.after_execute', $results);
```

---

## 6. Security Considerations

### 6.1 Input Validation

- All trigger/action fields are escaped via `db_escape()`
- JSON config is validated before parsing
- Field names are validated against entity schema

### 6.2 Access Control

- Uses FA page security (`$page_security`)
- Only users with SA_CUSTOMER can manage workflows

### 6.3 Action Execution Safety

- `calculate` action uses `eval()` only on numeric expressions
- `send_email` requires valid email in config
- Webhook URLs must be validated

---

## 7. Performance

### 7.1 Optimization Strategies

- Triggers are indexed by `entity_type` and `is_active`
- Actions are ordered by `action_order` in query
- Execution stops on first error (configurable)

### 7.2 Caching

- Trigger list can be cached during request
- Entity data should be prefetched

### 7.3 Scalability

- Suitable for small to medium deployments
- Large trigger sets should be filtered by entity_type
- Log table should be purged periodically

---

## 8. Error Handling

### 8.1 Evaluation Errors

- Invalid field name: return false, log warning
- Invalid operator: return false, log error
- Missing values: treat as null

### 8.2 Action Errors

- Invalid action type: return false, continue
- Action config parse error: return false, log error
- Action execution error: return false, log error, continue

### 8.3 Logging

- All errors are logged to fa_wf_log with status='error'
- Error messages are stored for debugging

---

## 9. Extension Points

### 9.1 Custom Operators

Add new operators to `evaluate_trigger()`:

```php
'between' => is_numeric($currentValue) && 
            $currentValue >= $min && $currentValue <= $max,
```

### 9.2 Custom Actions

Add new actions to `execute_action()`:

```php
'call_api' => wf_call_api($config, $entity),
```

### 9.3 Pre/Post Hooks

Add hooks in `run_workflows()`:

```php
// Before
crm_dispatch_event('workflow.before_execute', $context);

// After
crm_dispatch_event('workflow.after_execute', $results);
```

---

## 10. Testing Strategy

### 10.1 Unit Testing

- Test each operator individually
- Test each action type
- Test trigger evaluation logic

### 10.2 Integration Testing

- Test workflow execution end-to-end
- Test FA integration
- Test event dispatching

### 10.3 System Testing

- Test CRUD operations
- Test permissions
- Test UI functionality

---
*End of Architecture*
