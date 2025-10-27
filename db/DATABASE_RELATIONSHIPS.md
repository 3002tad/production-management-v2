# DATABASE RELATIONSHIP DIAGRAM
## Production Management System - Entity Relationship

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                     PRODUCTION MANAGEMENT DATABASE SCHEMA                    │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────────┐
│   CUSTOMER   │
│──────────────│
│ id_cust (PK) │
│ cust_name    │
│ address      │
│ telp         │
│ email        │
└──────┬───────┘
       │ 1
       │
       │ N
┌──────▼───────┐          ┌──────────────┐
│   PROJECT    │          │   PRODUCT    │
│──────────────│          │──────────────│
│id_project(PK)│◄─────────┤id_product(PK)│
│project_name  │    N   1 │product_name  │
│id_cust (FK)  │          │summary       │
│id_product(FK)│          │application   │
│diameter      │          │diameter      │
│qty_request   │          └──────────────┘
│entry_date    │
│pr_status     │
└──────┬───────┘
       │ 1
       ├──────────────────────┐
       │                      │
       │ N                    │ N
┌──────▼───────┐      ┌───────▼──────────┐
│   PLANNING   │      │ FINISHED_REPORT  │
│──────────────│      │──────────────────│
│ id_plan (PK) │      │ id_finished (PK) │
│ plan_name    │      │ id_project (FK)  │
│id_project(FK)│      │ total_finished   │
│ qty_target   │      │ fdate            │
│ end_date     │      └──────────────────┘
│ pl_status    │
└──────┬───────┘
       │ 1
       │
       │ N
┌──────▼────────┐          ┌──────────────┐          ┌──────────────┐
│  PLAN_SHIFT   │          │  SHIFTMENT   │          │    STAFF     │
│───────────────│          │──────────────│          │──────────────│
│id_planshift(PK)◄─────────┤id_shift (PK) │          │id_staff (PK) │
│id_plan (FK)   │    N   1 │shift_name    │          │staff_name    │
│id_shift (FK)  │          │start_time    │      ┌───┤phone         │
│id_staff (FK)  ├──────────┤end_time      │      │   │email         │
│start_date     │          └──────────────┘      │   │st_status     │
│ps_status      │                          1   N │   └──────────────┘
└───────┬───────┘                          ◄──────┘
        │ 1
        ├──────────────────────┬────────────────────┐
        │                      │                    │
        │ N                    │ N                  │ N
┌───────▼──────┐      ┌────────▼────────┐  ┌───────▼──────────┐
│  P_MACHINE   │      │   P_MATERIAL    │  │ SORTING_REPORT   │
│──────────────│      │─────────────────│  │──────────────────│
│id_pmachine(PK)│      │id_pmaterial (PK)│  │id_sorting (PK)   │
│id_planshift(FK)      │id_planshift (FK)│  │id_planshift (FK) │
│id_machine(FK)│      │id_material (FK) │  │waste             │
│mc_stats      │      │used_stock       │  │finished          │
└──────┬───────┘      └────────┬────────┘  └──────────────────┘
       │ N                     │ N
       │ 1                     │ 1
┌──────▼───────┐      ┌────────▼────────┐
│   MACHINE    │      │    MATERIAL     │
│──────────────│      │─────────────────│
│id_machine(PK)│      │id_material (PK) │
│machine_name  │      │material_name    │
│capacity      │      │stock            │
│mc_status     │      └─────────────────┘
└──────────────┘

┌──────────────┐
│     USER     │
│──────────────│
│ user_id (PK) │
│ username     │
│ password     │
│ role         │
└──────────────┘
(No FK - Standalone authentication table)

```

## RELATIONSHIP DETAILS

### 1:N Relationships (One-to-Many)

| Parent Table | Child Table | Relationship | Delete Policy |
|-------------|-------------|--------------|---------------|
| **customer** | project | 1 customer → N projects | RESTRICT |
| **product** | project | 1 product → N projects | RESTRICT |
| **project** | planning | 1 project → N plannings | CASCADE |
| **project** | finished_report | 1 project → N finished reports | CASCADE |
| **planning** | plan_shift | 1 planning → N plan shifts | CASCADE |
| **shiftment** | plan_shift | 1 shift → N plan shifts | RESTRICT |
| **staff** | plan_shift | 1 staff → N plan shifts | RESTRICT |
| **plan_shift** | p_machine | 1 plan shift → N machines used | CASCADE |
| **plan_shift** | p_material | 1 plan shift → N materials used | CASCADE |
| **plan_shift** | sorting_report | 1 plan shift → N sorting reports | CASCADE |
| **machine** | p_machine | 1 machine → N usages | RESTRICT |
| **material** | p_material | 1 material → N usages | RESTRICT |

### Foreign Key Naming Convention

Format: `fk_{child_table}_{parent_table}`

Examples:
- `fk_project_customer` → project references customer
- `fk_planshift_planning` → plan_shift references planning
- `fk_pmachine_planshift` → p_machine references plan_shift

### Delete Policies Explained

**RESTRICT**: Prevents deletion of parent if child records exist
- Used for: Master data (customer, product, machine, material, staff, shiftment)
- Example: Cannot delete a customer if they have projects

**CASCADE**: Automatically deletes all child records when parent is deleted
- Used for: Transaction data dependent on parent
- Example: Deleting a project removes all its plannings

**ON UPDATE CASCADE**: All foreign keys
- Automatically updates child records when parent key changes

## DATA FLOW

1. **Order Flow:**
   ```
   Customer → Project → Planning → Plan_Shift → Production → Sorting → Finished Report
   ```

2. **Resource Allocation:**
   ```
   Plan_Shift ─┬→ P_Machine → Machine
               └→ P_Material → Material
   ```

3. **Production Tracking:**
   ```
   Plan_Shift → Sorting_Report (waste + finished)
   Project → Finished_Report (accumulated totals)
   ```

## BUSINESS RULES (Enforced by FK)

1. **Cannot delete customer** if they have active projects
2. **Cannot delete product** if it's used in any project
3. **Cannot delete machine** if it's being used in production
4. **Cannot delete material** if it's allocated to production
5. **Cannot delete staff** if they're assigned to shifts
6. **Cannot delete shift** if it's being used in plans
7. **Deleting project** removes all related planning, reports
8. **Deleting planning** removes all plan_shift and production data
9. **Deleting plan_shift** removes machine/material usage and sorting reports

## INDEX STRUCTURE

All foreign keys automatically create indexes for optimal JOIN performance:
- `project.id_cust` (FK → customer)
- `project.id_product` (FK → product)
- `planning.id_project` (FK → project)
- `plan_shift.id_plan` (FK → planning)
- `plan_shift.id_shift` (FK → shiftment)
- `plan_shift.id_staff` (FK → staff)
- `p_machine.id_planshift` (FK → plan_shift)
- `p_machine.id_machine` (FK → machine)
- `p_material.id_planshift` (FK → plan_shift)
- `p_material.id_material` (FK → material)
- `sorting_report.id_planshift` (FK → plan_shift)
- `finished_report.id_project` (FK → project)

## BENEFITS OF FOREIGN KEYS

✅ **Data Integrity**: Prevents orphaned records
✅ **Cascading Operations**: Automatic cleanup of related data
✅ **Query Optimization**: Automatic indexes on FK columns
✅ **Documentation**: Self-documenting database structure
✅ **Visual Tools**: ERD diagrams in phpMyAdmin/MySQL Workbench
✅ **Error Prevention**: Database-level validation

---

**Created:** 27/10/2025  
**Database:** db_production  
**Engine:** InnoDB (Required for FK support)
