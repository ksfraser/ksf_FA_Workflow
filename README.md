# FA_Workflow - FrontAccounting Workflow Engine

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.0+-777bb6)
![FA](https://img.shields.io/badge/FrontAccounting-2.4.x-green)
![License](https://img.shields.io/badge/license-GPL--3.0-orange)

## Overview

FA_Workflow is a powerful workflow automation module for FrontAccounting that enables users to create triggers, conditions, and automated actions based on entity changes. The module provides a rule-based workflow engine that responds to data changes in the system.

### Features

- **Trigger System** - Define conditions that fire when entity fields change
- **Condition Operators** - Multiple comparison operators (equals, contains, greater than, etc.)
- **Automated Actions** - Execute actions when triggers fire (update fields, send emails, etc.)
- **Entity Support** - Works with Customers, Contacts, Opportunities, Leads, Tickets
- **Priority-based Execution** - Control trigger execution order
- **Execution Logging** - Audit trail of all workflow executions
- **Event Integration** - Can trigger PSR-14 events for cross-module integration

### Status

**IMPLEMENTED** - Ready for Testing

- Trigger/condition/action architecture
- Multiple comparison operators
- Action types: update_field, calculate, assign_to, send_email, trigger_event
- Execution logging
- Priority-based execution
- FrontAccounting integration

## Quick Start

### Installation

1. **Copy module files**:
```bash
cp -r FA_Workflow /path/to/frontaccounting/modules/
```

2. **Install via FrontAccounting**:
- Go to Administrator > Modules > Install Modules
- Find FA_Workflow and click Install

3. **Database tables** are created automatically on install

4. **Assign permissions** to users via Administrator > User Roles

### Using the Module

Access via the CRM menu after installation:

- **Workflows** - Create and manage triggers
- **Actions** - Configure actions for triggers

### Creating a Simple Workflow

1. Navigate to Workflows page
2. Click "New Trigger"
3. Configure:
   - Name: "Close Follow-up Tasks"
   - Entity Type: "Customer"
   - Field Name: "lead_status"
   - Operator: "equals"
   - Field Value: "converted"
   - Trigger Type: "on_update"
4. Save trigger
5. Go to Actions page
6. Select the trigger
7. Add action: "Update Field" with config: `{"field": "task_status", "value": "Closed"}`

## Database Tables

### Core Tables

| Table | Description |
|-------|-------------|
| `fa_wf_triggers` | Trigger definitions |
| `fa_wf_actions` | Action configurations |
| `fa_wf_workflows` | Workflow groupings |
| `fa_wf_log` | Execution audit log |

### fa_wf_triggers

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT | Primary key |
| `name` | VARCHAR(100) | Trigger name |
| `entity_type` | VARCHAR(20) | Target entity (debtor, contact, opportunity, ticket, lead) |
| `field_name` | VARCHAR(50) | Field to monitor |
| `operator` | VARCHAR(20) | Comparison operator |
| `field_value` | VARCHAR(255) | Value to compare against |
| `trigger_type` | VARCHAR(20) | When to fire (on_save, on_create, on_update, on_delete) |
| `is_active` | TINYINT | Active flag |
| `priority` | INT | Execution priority |

### fa_wf_actions

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT | Primary key |
| `trigger_id` | INT | Link to trigger |
| `name` | VARCHAR(100) | Action name |
| `action_type` | VARCHAR(30) | Action type |
| `action_config` | TEXT | JSON configuration |
| `action_order` | INT | Execution order |
| `is_active` | TINYINT | Active flag |

## Permissions

| Permission | Description |
|------------|-------------|
| `SA_CUSTOMER` | View workflow configurations |
| `SA_CUSTOMER` | Manage workflows and actions |

Note: Module uses existing FA security roles. Grant appropriate customer permissions to users.

## API Reference

### Database Functions

```php
// Trigger Management
add_trigger($trigger_data);
get_trigger($trigger_id);
get_triggers($entity_type, $active_only);
update_trigger($trigger_id, $trigger_data);
delete_trigger($trigger_id);

// Action Management
add_action($action_data);
get_actions($trigger_id);
update_action($action_id, $action_data);
delete_action($action_id);

// Workflow Execution
evaluate_trigger($trigger, $entity, $oldEntity);
execute_action($action, &$entity, $context);
run_workflows($entity_type, $entity, $oldEntity, $context);

// Logging
log_workflow_execution($workflow_id, $trigger_id, $action_id, $entity_type, $entity_id, $status, $error);
```

### Supported Operators

| Operator | Description | Example |
|----------|-------------|---------|
| `equals` | Field equals value | status = 'Active' |
| `not_equals` | Field not equals value | status != 'Closed' |
| `contains` | Field contains value | email contains '@company.com' |
| `is_empty` | Field is empty | notes is empty |
| `is_not_empty` | Field has value | notes is not empty |
| `changes` | Field changed | status changes |
| `changes_from` | Changed from specific value | status changes from 'New' |
| `greater_than` | Numeric greater than | amount > 1000 |
| `less_than` | Numeric less than | amount < 500 |

### Supported Action Types

| Action Type | Description |
|------------|-------------|
| `update_field` | Update a field value |
| `set_field` | Set a field to a value |
| `calculate` | Calculate field value using expression |
| `assign_to` | Assign entity to user |
| `trigger_event` | Trigger PSR-14 event |
| `send_email` | Send email notification |
| `add_note` | Add note to entity |
| `webhook` | Call external webhook |

## Events

The module integrates with PSR-14 events:

- `workflow.before_execute` - Before workflow runs
- `workflow.after_execute` - After workflow completes
- `workflow.trigger_fired` - Trigger condition met
- `workflow.action_executed` - Action completed

## Development

### Testing

```bash
# Run unit tests when available
./vendor/bin/phpunit tests/
```

### File Structure

```
FA_Workflow/
├── hooks.php           # Module installation hooks
├── includes/
│   └── wf_db.inc     # Database functions
├── pages/
│   ├── workflows.php  # Trigger management
│   └── actions.php   # Action configuration
└── ProjectDcs/
    ├── Architecture.md
    ├── Functional Requirements.md
    ├── Test Plan.md
    └── UAT Plan.md
```

## Configuration Examples

### Example 1: Auto-close Opportunities

When an opportunity status changes to "Won", automatically set close date:

```
Trigger:
  - entity_type: opportunity
  - field_name: status
  - operator: equals
  - field_value: Won
  - trigger_type: on_update

Action:
  - action_type: set_field
  - action_config: {"field": "actual_close_date", "value": "today"}
```

### Example 2: Assign New Customers

Route new customers to account manager based on territory:

```
Trigger:
  - entity_type: debtor
  - field_name: territory_id
  - operator: is_not_empty
  - trigger_type: on_create

Action:
  - action_type: calculate
  - action_config: {"target_field": "account_manager", "expression": "territory_map[{territory_id}]"}
```

### Example 3: Send Alert on High Value

Send email when opportunity value exceeds threshold:

```
Trigger:
  - entity_type: opportunity
  - field_name: estimated_value
  - operator: greater_than
  - field_value: 50000
  - trigger_type: on_save

Action:
  - action_type: send_email
  - action_config: {"to": "manager@company.com", "template": "high_value_alert"}
```

## Troubleshooting

### Common Issues

1. **Triggers not firing**
   - Verify trigger is active
   - Check entity type matches
   - Verify operator is correct

2. **Actions not executing**
   - Check action is active
   - Verify trigger fired
   - Check action_config JSON is valid

3. **Workflow errors**
   - Check execution log for errors
   - Verify field names exist on entity

4. **Permissions errors**
   - Grant SA_CUSTOMER permission to user

## Version History

| Version | Changes |
|---------|---------|
| 1.0.0 | Initial release with core trigger/action system |

## Requirements

- FrontAccounting 2.4.0+
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.0+

## License

Copyright (C) 2024 KSFII Development Team

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

## Support

For issues and feature requests, please open an issue on the project repository.

## Documentation

Full documentation is available in `ProjectDcs/`:

- [Functional Requirements](ProjectDcs/Functional%20Requirements.md)
- [Architecture](ProjectDcs/Architecture.md)
- [Test Plan](ProjectDcs/Test%20Plan.md)
- [UAT Plan](ProjectDcs/UAT%20Plan.md)

---
*FA_Workflow Module v1.0.0*
*For FrontAccounting 2.4.x*
