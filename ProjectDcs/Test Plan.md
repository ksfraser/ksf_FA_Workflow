# Test Plan - ksf_FA_Workflow

## Overview

This document outlines the test strategy, test types, test cases, and acceptance criteria for the FA_Workflow module.

---

## 1. Test Strategy

### 1.1 Test Objectives

- Verify all functional requirements are met
- Ensure data integrity and consistency
- Validate workflow execution logic
- Confirm security controls work correctly
- Achieve code quality standards

### 1.2 Test Levels

| Level | Description | Coverage Target |
|-------|-------------|-----------------|
| Unit Testing | Individual function/method testing | Core evaluation logic |
| Integration Testing | Module integration with FA | All entities |
| System Testing | End-to-end workflows | Critical paths |
| User Acceptance Testing | Business user validation | All use cases |

### 1.3 Test Types

| Type | Description |
|------|-------------|
| Functional Testing | Feature verification |
| Regression Testing | Existing functionality |
| Security Testing | Permission and access |
| Performance Testing | Response times |
| Error Handling | Invalid inputs, edge cases |

---

## 2. Test Environment

### 2.1 Environment Requirements

- FrontAccounting 2.4.0+ installed
- PHP 8.0+
- MySQL 5.7+
- Web browser (Chrome/Firefox/Edge)
- FA_Workflow module installed

### 2.2 Test Data

**Required Test Data**:

- Sample triggers (active and inactive)
- Actions with different action types
- Test entities (customer, opportunity)

---

## 3. Test Cases

### 3.1 Trigger Management Tests

#### TC-WF-001: Create Trigger

| Field | Value |
|-------|-------|
| Test ID | TC-WF-001 |
| Description | Create a new trigger with all fields |
| Preconditions | User has SA_CUSTOMER permission |
| Steps | 1. Navigate to Workflows page |
| | 2. Click "New Trigger" |
| | 3. Fill all fields |
| | 4. Click Save |
| Expected Result | Trigger saved to database, appears in list |
| Pass Criteria | Trigger visible in list with correct data |

#### TC-WF-002: View Trigger List

| Field | Value |
|-------|-------|
| Test ID | TC-WF-002 |
| Description | View list of all triggers |
| Preconditions | Triggers exist in database |
| Steps | 1. Navigate to Workflows page |
| | 2. View displayed list |
| Expected Result | Triggers displayed in table format |
| Pass Criteria | All columns display correctly |

#### TC-WF-003: Edit Trigger

| Field | Value |
|-------|-------|
| Test ID | TC-WF-003 |
| Description | Modify existing trigger |
| Preconditions | Trigger exists |
| Steps | 1. Click Edit on trigger |
| | 2. Modify field_name |
| | 3. Click Update |
| Expected Result | Trigger updated |
| Pass Criteria | Changes reflected in list |

#### TC-WF-004: Delete Trigger

| Field | Value |
|-------|-------|
| Test ID | TC-WF-004 |
| Description | Delete trigger |
| Preconditions | Trigger has associated actions |
| Steps | 1. Click Delete on trigger |
| | 2. Confirm deletion |
| Expected Result | Trigger deleted, actions also deleted |
| Pass Criteria | Trigger not in list |

#### TC-WF-005: Toggle Trigger Active

| Field | Value |
|-------|-------|
| Test ID | TC-WF-005 |
| Description | Toggle trigger active status |
| Preconditions | Trigger is active |
| Steps | 1. Edit trigger |
| | 2. Uncheck Active |
| | 3. Save |
| Expected Result | Trigger marked inactive |
| Pass Criteria | Trigger does not execute |

### 3.2 Operator Evaluation Tests

#### TC-WF-010: Equals Operator

| Field | Value |
|-------|-------|
| Test ID | TC-WF-010 |
| Description | Test equals operator |
| Steps | Evaluate with entity having matching value |
| Expected Result | Returns true |
| Pass Criteria | [ ] Returns true when values match |

#### TC-WF-011: Not Equals Operator

| Field | Value |
|-------|-------|
| Test ID | TC-WF-011 |
| Description | Test not_equals operator |
| Steps | Evaluate with entity having different value |
| Expected Result | Returns true |
| Pass Criteria | [ ] Returns true when values differ |

#### TC-WF-012: Contains Operator

| Field | Value |
|-------|-------|
| Test ID | TC-WF-012 |
| Description | Test contains operator |
| Steps | Evaluate with entity containing substring |
| Expected Result | Returns true |
| Pass Criteria | [ ] Returns true when substring found |

#### TC-WF-013: Is Empty Operator

| Field | Value |
|-------|-------|
| Test ID | TC-WF-013 |
| Description | Test is_empty operator |
| Steps | Evaluate with empty/null field |
| Expected Result | Returns true |
| Pass Criteria | [ ] Returns true for empty values |

#### TC-WF-014: Changes Operator

| Field | Value |
|-------|-------|
| Test ID | TC-WF-014 |
| Description | Test changes operator |
| Steps | Evaluate with oldEntity and new entity |
| Expected Result | Returns true |
| Pass Criteria | [ ] Returns true when value changed |

#### TC-WF-015: Greater Than Operator

| Field | Value |
|-------|-------|
| Test ID | TC-WF-015 |
| Description | Test greater_than operator |
| Steps | Evaluate with value > threshold |
| Expected Result | Returns true |
| Pass Criteria | [ ] Returns true when value exceeds threshold |

#### TC-WF-016: Less Than Operator

| Field | Value |
|-------|-------|
| Test ID | TC-WF-016 |
| Description | Test less_than operator |
| Steps | Evaluate with value < threshold |
| Expected Result | Returns true |
| Pass Criteria | [ ] Returns true when value below threshold |

### 3.3 Action Execution Tests

#### TC-WF-020: Update Field Action

| Field | Value |
|-------|-------|
| Test ID | TC-WF-020 |
| Description | Test update_field action |
| Steps | Execute with config: {"field": "status", "value": "Closed"} |
| Expected Result | Entity field updated |
| Pass Criteria | [ ] Entity status = 'Closed' |

#### TC-WF-021: Set Field Action

| Field | Value |
|-------|-------|
| Test ID | TC-WF-021 |
| Description | Test set_field action |
| Steps | Execute with config: {"field": "assigned_to", "value": "user1"} |
| Expected Result | Entity field set |
| Pass Criteria | [ ] Entity assigned_to = 'user1' |

#### TC-WF-022: Calculate Action

| Field | Value |
|-------|-------|
| Test ID | TC-WF-022 |
| Description | Test calculate action |
| Steps | Execute with expression |
| Expected Result | Field calculated |
| Pass Criteria | [ ] Result matches expected value |

#### TC-WF-023: Assign To Action

| Field | Value |
|-------|-------|
| Test ID | TC-WF-023 |
| Description | Test assign_to action |
| Steps | Execute with user config |
| Expected Result | Entity assigned to user |
| Pass Criteria | [ ] assigned_to field updated |

### 3.4 Workflow Execution Tests

#### TC-WF-030: Single Trigger Execution

| Field | Value |
|-------|-------|
| Test ID | TC-WF-030 |
| Description | Run workflows with single matching trigger |
| Steps | Call run_workflows with entity |
| Expected Result | Trigger fires, actions execute |
| Pass Criteria | [ ] triggers_fired contains trigger ID |

#### TC-WF-031: Multiple Trigger Execution

| Field | Value |
|-------|-------|
| Test ID | TC-WF-031 |
| Description | Multiple triggers fire on same entity |
| Steps | Run workflows with multiple matching triggers |
| Expected Result | All matching triggers fire |
| Pass Criteria | [ ] All matching triggers in results |

#### TC-WF-032: No Matching Triggers

| Field | Value |
|-------|-------|
| Test ID | TC-WF-032 |
| Description | No triggers match entity |
| Steps | Run workflows with no matching triggers |
| Expected Result | No triggers fire |
| Pass Criteria | [ ] triggers_fired is empty |

### 3.5 Logging Tests

#### TC-WF-040: Execution Logging

| Field | Value |
|-------|-------|
| Test ID | TC-WF-040 |
| Description | Verify execution is logged |
| Steps | Run workflow, check log table |
| Expected Result | Log entry created |
| Pass Criteria | [ ] Log entry exists with correct data |

#### TC-WF-041: Error Logging

| Field | Value |
|-------|-------|
| Test ID | TC-WF-041 |
| Description | Verify errors are logged |
| Steps | Execute action that fails |
| Expected Result | Error logged with status='error' |
| Pass Criteria | [ ] Error message stored |

### 3.6 Integration Tests

#### TC-WF-050: Debtor Entity Integration

| Field | Value |
|-------|-------|
| Test ID | TC-WF-050 |
| Description | Workflow works with debtor entity |
| Steps | Run workflows on debtor entity |
| Expected Result | Triggers evaluate correctly |
| Pass Criteria | [ ] Debtor-specific triggers work |

#### TC-WF-051: Opportunity Entity Integration

| Field | Value |
|-------|-------|
| Test ID | TC-WF-051 |
| Description | Workflow works with opportunity entity |
| Steps | Run workflows on opportunity entity |
| Expected Result | Triggers evaluate correctly |
| Pass Criteria | [ ] Opportunity triggers work |

---

## 4. Test Data Requirements

### 4.1 Test Triggers

| Name | Entity Type | Field | Operator | Value |
|------|------------|-------|----------|-------|
| Test Trigger 1 | debtor | lead_status | equals | new |
| Test Trigger 2 | opportunity | status | not_equals | Closed |
| Test Trigger 3 | debtor | territory_id | is_not_empty | - |

### 4.2 Test Actions

| Name | Trigger | Type | Config |
|------|---------|------|--------|
| Test Action 1 | Test Trigger 1 | update_field | {"field": "status", "value": "active"} |
| Test Action 2 | Test Trigger 2 | set_field | {"field": "priority", "value": "high"} |

---

## 5. Acceptance Criteria Summary

### 5.1 Trigger Management

- [ ] Can create trigger with all required fields
- [ ] Can view trigger list
- [ ] Can edit existing trigger
- [ ] Can delete trigger (includes cascade)
- [ ] Can toggle active status

### 5.2 Operators

- [ ] equals works correctly
- [ ] not_equals works correctly
- [ ] contains works correctly
- [ ] is_empty works correctly
- [ ] is_not_empty works correctly
- [ ] changes works correctly
- [ ] changes_from works correctly
- [ ] greater_than works correctly
- [ ] less_than works correctly

### 5.3 Actions

- [ ] update_field modifies entity
- [ ] set_field sets value
- [ ] calculate evaluates expression
- [ ] assign_to updates assignment
- [ ] Actions execute in order

### 5.4 Workflow Execution

- [ ] run_workflows executes triggers
- [ ] Matching triggers fire
- [ ] Actions execute correctly
- [ ] Execution logged
- [ ] Errors handled gracefully

### 5.5 Integration

- [ ] Works with debtor entities
- [ ] Works with opportunity entities
- [ ] Events dispatched

---

## 6. Test Execution Schedule

| Phase | Duration | Dependencies |
|-------|----------|--------------|
| Unit Testing | 1 day | Code complete |
| Integration Testing | 2 days | Unit tests pass |
| System Testing | 2 days | Integration pass |
| UAT | 3 days | System test pass |

---
*End of Test Plan*
