# CAP2 - UC15_BCSC (Worker Incident Management) Migrations

## 📋 Overview

This folder contains all database migrations specific to the **UC15_BCSC** module (Worker Incident Management) for **CAP2** (Phase 2) development.

## 📁 Structure

```
db/migrations/Cap2/UC15_BCSC/
├── README.md                                    ← This file
├── 001_fix_worker_role_and_seed_incidents.sql  ← Worker setup migration
└── (future migrations numbered 002, 003, etc.)
```

## 🚀 How to Run Migrations

### **Option 1: PowerShell (Windows)**

```powershell
# Run all CAP2 UC15_BCSC migrations
Get-ChildItem "db/migrations/Cap2/UC15_BCSC/*.sql" | Sort-Object Name | ForEach-Object {
    Write-Host "Running: $($_.Name)"
    Get-Content $_ | & "C:\xampp\mysql\bin\mysql.exe" -h localhost -u root
}
```

### **Option 2: MySQL CLI**

```bash
mysql -u root -p db_production < db/migrations/Cap2/UC15_BCSC/001_fix_worker_role_and_seed_incidents.sql
```

### **Option 3: phpMyAdmin**

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database: `db_production`
3. Go to **SQL** tab
4. Copy-paste contents of migration file
5. Click **Go**

## 📝 Migrations List

| # | File | Description | Status |
|---|------|-------------|--------|
| 001 | `001_fix_worker_role_and_seed_incidents.sql` | Fix worker role from 'admin' to 'worker' + seed test data | ✅ Ready |
| 002 | `002_create_incident_reports_table.sql` | Create incident_reports table with foreign keys and indexes | ✅ Ready |

## ✅ Verification

After running migrations, verify:

```sql
-- Check worker role
SELECT user_id, username, role_id, role FROM user WHERE role_id=7;

-- Check incident data
SELECT COUNT(*) as total_incidents FROM incident_reports;

-- Expected results:
-- ✓ 2 worker users with role='worker'
-- ✓ 6+ incident records
```

## 🔄 Idempotency

All migrations in this folder are **idempotent** - they are safe to run multiple times without causing errors or duplicates.

Migration 001 specifically:
- Only updates role if currently different from 'worker'
- Only inserts incidents if table has < 3 records
- Won't create duplicates on re-run

## 📚 Related Documentation

- Parent: `db/migrations/README.md` - All migrations overview
- Feature: UC15_BCSC Worker Incident Management
- Related docs in project root:
  - `DOCUMENTATION_INDEX.md`
  - `QUICK_REFERENCE.md`

## 🔧 Adding New Migrations

When adding new UC15_BCSC migrations:

1. Name format: `00X_descriptive_name.sql` (e.g., `002_add_incident_categories.sql`)
2. Include comments explaining purpose
3. Make migration idempotent
4. Include verification queries
5. Update this README.md

## 🐛 Troubleshooting

**Issue:** Migration fails with foreign key error
- Solution: Ensure referenced tables/records exist (user, machine, plan_shift)
- Check: `SELECT * FROM user WHERE role_id=7;`

**Issue:** No data inserted
- Check: incident_reports table row count (should be 3+ after migration)
- Run: Verification queries above

**Issue:** Worker login fails
- Check: User role is 'worker' (not 'admin')
- Run migration 001

---

**Last Updated:** 2025-11-30  
**Created by:** Production Management Team  
**Module:** UC15_BCSC (Worker Incident Management)
