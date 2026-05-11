# UAT Plan - ksf_FA_Workflow

## Overview

This document defines the User Acceptance Test (UAT) cases for the FA_Workflow module. UAT validates that the system meets business requirements and is ready for production deployment.

---

## 1. UAT Objectives

### 1.1 Goals

- Validate business workflows function correctly
- Confirm user requirements are met
- Ensure integration with FA works seamlessly
- Verify data accuracy and integrity
- Obtain sign-off for production deployment

### 1.2 Success Criteria

- All critical test cases pass
- No high-severity defects open
- User acceptance obtained
- Sign-off documented

---

## 2. UAT Scope

### 2.1 In Scope

- Trigger CRUD operations
- Condition operators
- Action execution
- Workflow execution
- Integration with FA entities
- Security and permissions

### 2.2 Out of Scope

- Performance stress testing
- Security penetration testing
- Browser compatibility (covered in QA)
- Custom operator development

---

## 3. UAT User Roles

| Role | Description | Tests Executed |
|------|-------------|----------------|
| CRM Manager | Configure and manage workflows | WF-001 through WF-010 |
| System Administrator | System configuration | AD-001 through AD-003 |
| Sales User | Trigger execution via entity changes | SU-001 through SU-003 |

---

## 4. UAT Test Cases

### 4.1 Trigger Management (WF)

#### UAT-WF-001: Create New Trigger

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-001 |
| Scenario | Create a new trigger as CRM Manager |
| Preconditions | User has CRM permission |
| Test Steps | 1. Login as CRM Manager |
| | 2. Navigate to Workflows |
| | 3. Click "New Trigger" |
| | 4. Enter: Name="Auto-close", Entity Type="opportunity" |
| | 5. Field="status", Operator="equals", Value="Won" |
| | 6. Trigger Type="on_update" |
| | 7. Click Save |
| Expected Result | Success message, trigger appears in list |
| Acceptance Criteria | [ ] Trigger saved to database |
| | [ ] Trigger visible in list with all fields correct |
| Result | PASS/FAIL |
| Notes | |

#### UAT-WF-002: Edit Trigger

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-002 |
| Scenario | Modify trigger details |
| Preconditions | Trigger exists from UAT-WF-001 |
| Test Steps | 1. Click Edit on "Auto-close" trigger |
| | 2. Change operator to "not_equals" |
| | 3. Change status to "Closed" |
| | 4. Save changes |
| Expected Result | Changes saved successfully |
| Acceptance Criteria | [ ] Operator changed to not_equals |
| | [ ] Field value changed to Closed |
| Result | PASS/FAIL |
| Notes | |

#### UAT-WF-003: Delete Trigger

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-003 |
| Scenario | Delete trigger |
| Preconditions | Trigger exists with actions |
| Test Steps | 1. Click Delete on "Auto-close" trigger |
| | 2. Confirm deletion |
| Expected Result | Trigger deleted |
| Acceptance Criteria | [ ] Trigger removed from list |
| | [ ] Associated actions deleted |
| Result | PASS/FAIL |
| Notes | |

#### UAT-WF-004: Toggle Trigger Active

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-004 |
| Scenario | Deactivate trigger |
| Preconditions | Active trigger exists |
| Test Steps | 1. Edit trigger |
| | 2. Uncheck "Active" |
| | 3. Save |
| Expected Result | Trigger marked inactive |
| Acceptance Criteria | [ ] Trigger does not appear in active list |
| | [ ] Trigger does not execute |
| Result | PASS/FAIL |
| Notes | |

#### UAT-WF-005: View Trigger List

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-005 |
| Scenario | View all triggers |
| Preconditions | Multiple triggers exist |
| Test Steps | 1. Navigate to Workflows |
| | 2. View the list |
| Expected Result | All triggers displayed |
| Acceptance Criteria | [ ] All columns display correctly |
| | [ ] Sorting works |
| Result | PASS/FAIL |
| Notes | |

### 4.2 Action Management (AC)

#### UAT-WF-006: Add Action to Trigger

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-006 |
| Scenario | Create action for trigger |
| Preconditions | Trigger exists |
| Test Steps | 1. Navigate to Actions |
| | 2. Select trigger |
| | 3. Click "New Action" |
| | 4. Name="Set Priority", Type="set_field" |
| | 5. Config: {"field": "priority", "value": "high"} |
| | 6. Save |
| Expected Result | Action created |
| Acceptance Criteria | [ ] Action saved to database |
| | [ ] Action associated with trigger |
| Result | PASS/FAIL |
| Notes | |

#### UAT-WF-007: Reorder Actions

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-007 |
| Scenario | Change action execution order |
| Preconditions | Multiple actions exist |
| Test Steps | 1. Edit first action |
| | 2. Change order to 2 |
| | 3. Edit second action |
| | 4. Change order to 1 |
| | 5. Save |
| Expected Result | Actions reordered |
| Acceptance Criteria | [ ] Actions execute in new order |
| Result | PASS/FAIL |
| Notes | |

#### UAT-WF-008: Delete Action

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-008 |
| Scenario | Delete action |
| Preconditions | Action exists |
| Test Steps | 1. Click Delete on action |
| | 2. Confirm |
| Expected Result | Action deleted |
| Acceptance Criteria | [ ] Action removed from trigger |
| Result | PASS/FAIL |
| Notes | |

### 4.3 Workflow Execution (EX)

#### UAT-WF-009: Trigger Fires on Entity Update

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-009 |
| Scenario | Trigger executes when entity updated |
| Preconditions | Trigger with on_update exists |
| Test Steps | 1. Update an opportunity |
| | 2. Change status to trigger value |
| | 3. Save |
| Expected Result | Workflow triggers, action executes |
| Acceptance Criteria | [ ] Trigger fires |
| | [ ] Action modifies entity |
| | [ ] Execution logged |
| Result | PASS/FAIL |
| Notes | |

#### UAT-WF-010: Multiple Triggers Execute

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-010 |
| Scenario | Multiple triggers fire on same change |
| Preconditions | Multiple triggers match same entity |
| Test Steps | 1. Update entity with changes |
| | 2. That match multiple triggers |
| | 3. Save |
| Expected Result | All matching triggers fire |
| Acceptance Criteria | [ ] All triggers in firing list |
| | [ ] All actions executed |
| Result | PASS/FAIL |
| Notes | |

### 4.4 Business Scenarios (BS)

#### UAT-WF-011: Auto-close Won Opportunities

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-011 |
| Scenario | Automatically close opportunity when won |
| Preconditions | Trigger and action configured |
| Test Steps | 1. Create trigger: status=equals=Won |
| | 2. Create action: set actual_close_date=today |
| | 3. Update opportunity status to Won |
| | 4. Save |
| Expected Result | Close date set automatically |
| Acceptance Criteria | [ ] Close date populated |
| | [ ] Execution logged |
| Result | PASS/FAIL |
| Notes | |

#### UAT-WF-012: Assign New High-value Leads

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-012 |
| Scenario | Route high-value leads to manager |
| Preconditions | Trigger configured for opportunity value |
| Test Steps | 1. Create trigger: estimated_value > 50000 |
| | 2. Action: assign_to Manager |
| | 3. Create high-value opportunity |
| | 4. Save |
| Expected Result | Opportunity assigned to manager |
| Acceptance Criteria | [ ] assigned_to field set |
| Result | PASS/FAIL |
| Notes | |

#### UAT-WF-013: Notify on Status Change

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-013 |
| Scenario | Send notification when status changes |
| Preconditions | Email action configured |
| Test Steps | 1. Configure send_email action |
| | 2. Change entity status |
| | 3. Save |
| Expected Result | Email sent |
| Acceptance Criteria | [ ] Email logged/sent |
| Result | PASS/FAIL |
| Notes | |

### 4.5 Integration Tests (IT)

#### UAT-WF-014: Integration with CRM Module

| Field | Value |
|-------|-------|
| Test Case ID | UAT-WF-014 |
| Scenario | Workflow integrates with CRM entities |
| Preconditions | FA_CRM module installed |
| Test Steps | 1. Create trigger for debtor |
| | 2. Update customer |
| | 3. Save |
| Expected Result | Trigger evaluates correctly |
| Acceptance Criteria | [ ] CRM-specific fields accessible |
| Result | PASS/FAIL |
| Notes | |

### 4.6 Administration (AD)

#### UAT-AD-001: View Execution Log

| Field | Value |
|-------|-------|
| Test Case ID | UAT-AD-001 |
| Scenario | View workflow execution log |
| Preconditions | Workflows have executed |
| Test Steps | 1. Navigate to Administration |
| | 2. View logs |
| Expected Result | Log entries displayed |
| Acceptance Criteria | [ ] Logs show execution history |
| | [ ] Errors captured |
| Result | PASS/FAIL |
| Notes | |

#### UAT-AD-002: Security Permissions

| Field | Value |
|-------|-------|
| Test Case ID | UAT-AD-002 |
| Scenario | User without permission cannot manage |
| Preconditions | User without CRM permission |
| Test Steps | 1. Login as restricted user |
| | 2. Try to access Workflows |
| Expected Result | Access denied |
| Acceptance Criteria | [ ] Permission check enforced |
| Result | PASS/FAIL |
| Notes | |

---

## 5. Test Execution Checklist

### 5.1 Before Testing

- [ ] Module installed and activated
- [ ] Test user accounts created
- [ ] Sample data loaded
- [ ] Test environment verified

### 5.2 During Testing

- [ ] Each test case executed
- [ ] Results recorded
- [ ] Defects logged
- [ ] Screenshots captured if needed

### 5.3 After Testing

- [ ] All test cases completed
- [ ] Defects addressed
- [ ] Results reviewed
- [ ] Sign-off requested

---

## 6. Defect Reporting

| Priority | Description | Must Fix for UAT? |
|----------|--------------|---------------------|
| Critical | System crashes, data loss | Yes |
| High | Major feature broken | Yes |
| Medium | Feature partially works | Yes |
| Low | Minor issue | No |

---

## 7. Sign-off Criteria

### 7.1 Completion Requirements

- [ ] All critical test cases pass
- [ ] All high-priority defects resolved
- [ ] No regression in existing functionality
- [ ] Documentation complete

### 7.2 Sign-off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| CRM Manager | | | |
| System Admin | | | |
| Project Lead | | | |

---

## 8. Test Results Summary

| Test Category | Total | Passed | Failed | Pass Rate |
|---------------|-------|--------|--------|----------|
| Trigger Management | 5 | | | |
| Action Management | 3 | | | |
| Workflow Execution | 2 | | | |
| Business Scenarios | 3 | | | |
| Integration | 1 | | | |
| Administration | 2 | | | |
| **Total** | **16** | | | |

---

## 9. Notes and Observations

### 9.1 Findings

_Record any observations during testing_

### 9.2 Recommendations

_Record any recommendations for improvement_

---
*End of UAT Plan*
