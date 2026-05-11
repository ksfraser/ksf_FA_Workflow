# AGENTS.md - ksf_FA_Workflow#

## Architecture Overview#

**FA Module** for Workflow Management - customizable workflows, approvals, and automation.

### Core Principles#
- **SOLID**, **DRY**, **TDD**, **DI**, **SRP**#

## Repository Structure#

```
ksf_FA_Workflow/
├── sql/#
│   ├── fa_workflows.sql#
│   ├── fa_workflow_steps.sql#
│   └── fa_workflow_approvals.sql#
├── includes/#
│   ├── workflows_db.inc#
│   ├── steps_db.inc#
│   └── approvals_db.inc#
├── pages/#
├── hooks.php#
├── composer.json#
└── ProjectDocs/#
```

## Dependencies#

- **ksf_FA_Workflow_Core** (business logic)#
- **FrontAccounting 2.4+**#
