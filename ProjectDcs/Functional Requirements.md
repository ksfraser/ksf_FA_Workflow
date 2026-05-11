# Functional Requirements - ksf_FA_Workflow

## Document Information

- **Module**: FA_Workflow (Workflow Engine)
- **Version**: 1.0.0
- **Date**: 2024-04-26
- **Status**: Implemented
- **Author**: KSFII Development Team

## 1. Overview

### 1.1 Purpose

This document defines the functional requirements for the FA_Workflow module, which provides a rule-based workflow automation engine for FrontAccounting.

### 1.2 Scope

The workflow module provides:

- Trigger definitions based on entity field changes
- Multiple condition operators for flexible matching
- Automated action execution
- Execution logging and audit trail
- Integration with FA entities and events

---

## 2. Trigger Management

### FR-WF-01: Create Trigger

**Requirement**: The system shall allow users to create workflow triggers.

**Fields**:

- `name` - Trigger name (required, unique)
- `entity_type` - Target entity type (debtor, contact, opportunity, ticket, lead)
- `field_name` - Field to monitor
- `operator` - Comparison operator
- `field_value` - Value to compare against
- `trigger_type` - When to fire (on_save, on_create, on_update, on_delete)
- `is_active` - Active flag
- `priority` - Execution priority

**Priority**: High

### FR-WF-02: View Triggers

**Requirement**: The system shall allow users to view trigger list and details.

**Features**:

- List all triggers with key fields
- Filter by entity type
- Filter by active status
- Sort by priority

**Priority**: High

### FR-WF-03: Edit Trigger

**Requirement**: The system shall allow users to modify existing triggers.

**Features**:

- Pre-populate form with existing values
- Validate required fields
- Preserve action associations

**Priority**: High

### FR-WF-04: Delete Trigger

**Requirement**: The system shall allow users to delete triggers.

**Features**:

- Confirmation before deletion
- Cascade delete associated actions
- Log deletion to audit trail

**Priority**: High

### FR-WF-05: Trigger Activation

**Requirement**: The system shall allow users to enable/disable triggers.

**Features**:

- Toggle active flag
- Inactive triggers do not execute

**Priority**: Medium

---

## 3. Condition Operators

### FR-WF-10: Equality Operators

**Requirement**: The system shall support equality-based conditions.

**Operators**:

- `equals` - Field equals value exactly
- `not_equals` - Field does not equal value

**Priority**: High

### FR-WF-11: String Operators

**Requirement**: The system shall support string matching.

**Operators**:

- `contains` - Field contains substring
- `is_empty` - Field is null or empty
- `is_not_empty` - Field has value

**Priority**: High

### FR-WF-12: Change Operators

**Requirement**: The system shall detect value changes.

**Operators**:

- `changes` - Field value changed
- `changes_from` - Changed from specific value to another

**Priority**: High

### FR-WF-13: Numeric Operators

**Requirement**: The system shall support numeric comparisons.

**Operators**:

- `greater_than` - Value exceeds threshold
- `less_than` - Value below threshold

**Priority**: Medium

---

## 4. Action Management

### FR-WF-20: Create Action

**Requirement**: The system shall allow users to create actions linked to triggers.

**Fields**:

- `trigger_id` - Link to trigger (required)
- `name` - Action name
- `action_type` - Type of action to execute
- `action_config` - JSON configuration
- `action_order` - Execution order
- `is_active` - Active flag

**Priority**: High

### FR-WF-21: Execute Actions

**Requirement**: The system shall execute actions when triggers fire.

**Action Types**:

- `update_field` - Update field to value
- `set_field` - Set field to value
- `calculate` - Calculate field from expression
- `assign_to` - Assign to user
- `trigger_event` - Trigger PSR-14 event
- `send_email` - Send email notification
- `add_note` - Add note
- `webhook` - Call webhook

**Priority**: High

### FR-WF-22: Action Order

**Requirement**: The system shall support ordered action execution.

**Features**:

- Execute actions in action_order sequence
- Stop on error if configured

**Priority**: Medium

### FR-WF-23: Delete Action

**Requirement**: The system shall allow users to delete actions.

**Priority**: High

---

## 5. Workflow Execution

### FR-WF-30: Run Workflows

**Requirement**: The system shall execute workflows when called.

**Process**:

1. Get all active triggers for entity type
2. Evaluate each trigger condition
3. For matching triggers, execute actions in order
4. Log execution results

**Priority**: High

### FR-WF-31: Trigger Evaluation

**Requirement**: The system shall evaluate trigger conditions correctly.

**Features**:

- Support all defined operators
- Handle null values gracefully
- Compare old and new entity values

**Priority**: High

### FR-WF-32: Action Execution

**Requirement**: The system shall execute actions properly.

**Features**:

- Parse action_config JSON
- Execute defined action type
- Handle execution errors

**Priority**: High

---

## 6. Logging and Audit

### FR-WF-40: Execution Logging

**Requirement**: The system shall log all workflow executions.

**Logged Data**:

- Workflow ID
- Trigger ID
- Action ID
- Entity type and ID
- Status (success/error)
- Error message if applicable
- Timestamp

**Priority**: Medium

### FR-WF-41: Log Query

**Requirement**: The system shall allow querying execution logs.

**Features**:

- Query by workflow
- Query by entity
- Query by date range

**Priority**: Low

---

## 7. Integration

### FR-WF-50: Entity Integration

**Requirement**: The system shall work with FA entities.

**Supported Entities**:

- debtor (Customer)
- contact (Contact)
- opportunity (Opportunity)
- lead (Lead)
- ticket (Ticket)

**Priority**: High

### FR-WF-51: Event Integration

**Requirement**: The system shall integrate with PSR-14 events.

**Events**:

- workflow.before_execute
- workflow.after_execute
- workflow.trigger_fired
- workflow.action_executed

**Priority**: Low

---

## 8. Data Model

### 8.1 Trigger Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | INT | Auto | Primary key |
| name | VARCHAR(100) | Yes | Trigger name |
| entity_type | VARCHAR(20) | Yes | Target entity |
| field_name | VARCHAR(50) | Yes | Monitored field |
| operator | VARCHAR(20) | Yes | Comparison operator |
| field_value | VARCHAR(255) | No | Comparison value |
| trigger_type | VARCHAR(20) | Yes | When to fire |
| is_active | TINYINT | No | Active flag (default 1) |
| priority | INT | No | Execution order |
| created_at | TIMESTAMP | Auto | Creation time |
| updated_at | TIMESTAMP | Auto | Last update |

### 8.2 Action Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | INT | Auto | Primary key |
| trigger_id | INT | Yes | Link to trigger |
| name | VARCHAR(100) | Yes | Action name |
| action_type | VARCHAR(30) | Yes | Action type |
| action_config | TEXT | No | JSON config |
| action_order | INT | No | Execution order |
| is_active | TINYINT | No | Active flag |
| created_at | TIMESTAMP | Auto | Creation time |
| updated_at | TIMESTAMP | Auto | Last update |

### 8.3 Log Fields

| Field | Type | Description |
|-------|------|-------------|
| id | INT | Primary key |
| workflow_id | INT | Workflow reference |
| trigger_id | INT | Trigger reference |
| action_id | INT | Action reference |
| entity_type | VARCHAR(20) | Entity type |
| entity_id | INT | Entity ID |
| status | VARCHAR(20) | Success/error |
| error_message | TEXT | Error details |
| executed_at | TIMESTAMP | Execution time |

---

## 9. Non-Functional Requirements

### NFR-01: Performance

- Triggers evaluate in < 100ms
- Actions execute in < 500ms
- Log queries return in < 1 second

### NFR-02: Security

- Only authorized users can manage triggers
- Action config cannot execute arbitrary PHP (except calculate)
- SQL injection prevention via db_escape()

### NFR-03: Reliability

- Failed actions should not block other actions
- Execution continues on action error
- All executions are logged

---

## 10. Acceptance Criteria

### AC-WF-01: Trigger CRUD

- [ ] Can create trigger with all fields
- [ ] Can view trigger list
- [ ] Can edit existing trigger
- [ ] Can delete trigger (cascades to actions)
- [ ] Can toggle trigger active status

### AC-WF-02: Condition Evaluation

- [ ] equals returns true when values match
- [ ] not_equals returns true when values differ
- [ ] contains finds substring
- [ ] is_empty detects null/empty
- [ ] changes detects value modification

### AC-WF-03: Action Execution

- [ ] update_field modifies entity field
- [ ] set_field sets field value
- [ ] calculate evaluates expression
- [ ] Actions execute in defined order

### AC-WF-04: Workflow Execution

- [ ] run_workflows executes matching triggers
- [ ] Actions execute when trigger fires
- [ ] Execution logged to fa_wf_log

### AC-WF-05: Integration

- [ ] Works with debtor entity
- [ ] Works with opportunity entity
- [ ] Events dispatched correctly

---
*End of Functional Requirements*
