# Use Cases - ksf_FA_Workflow

## Reference Use Cases
- Core UC: ksf_Workflow/ProjectDcs/Use Case.md (UC-WF-001 through UC-WF-010)

---

## UC-FA-WF-001: FA Hook Integration
**Actor**: System

**FA-Specific Flow**:
1. FA event occurs (invoice approval, etc.)
2. ksf_FA_Workflow calls `hook_invoke_all()`
3. ksf_Workflow processes workflow
4. Result returned to FA

---

## UC-FA-WF-002: Dimension-Based Approval
**Actor**: Finance Manager

**FA-Specific Flow**:
1. Invoice requires approval
2. ksf_FA_Workflow:
   - Checks invoice dimension (department)
   - Routes to dimension approver
   - Tracks approval in FA audit trail

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*