# CAP2 (Phase 2) Database Migrations

## 📋 Overview

This folder contains all database migrations for **CAP2** (Phase 2) development, organized by feature/module.

## 📁 Structure

```
db/migrations/Cap2/
├── README.md                           ← This file
├── UC15_BCSC/                          ← Worker Incident Management
│   ├── README.md
│   ├── 001_fix_worker_role_and_seed_incidents.sql
│   └── (future: 002, 003, etc.)
└── (future modules)
```

## 🚀 Available Modules

### **UC15_BCSC - Worker Incident Management**
- **Path:** `Cap2/UC15_BCSC/`
- **Purpose:** Manages incident reports from production workers
- **Migrations:** 
  - `001_fix_worker_role_and_seed_incidents.sql` - Setup worker role & test data
- **Documentation:** See `UC15_BCSC/README.md`

## 🔧 How to Run All CAP2 Migrations

### **Option 1: Run All Modules**

```powershell
# PowerShell - Run all CAP2 migrations in order
$modules = @("UC15_BCSC") # Add more modules as needed

foreach ($module in $modules) {
    Write-Host "Running migrations for $module..." -ForegroundColor Green
    Get-ChildItem "db/migrations/Cap2/$module/*.sql" | Sort-Object Name | ForEach-Object {
        Write-Host "  → $($_.Name)"
        Get-Content $_ | & "C:\xampp\mysql\bin\mysql.exe" -h localhost -u root
    }
}
```

### **Option 2: Run Specific Module**

```powershell
# Run only UC15_BCSC migrations
Get-ChildItem "db/migrations/Cap2/UC15_BCSC/*.sql" | Sort-Object Name | ForEach-Object {
    Write-Host "Running: $($_.Name)"
    Get-Content $_ | & "C:\xampp\mysql\bin\mysql.exe" -h localhost -u root
}
```

### **Option 3: Run Single Migration**

```powershell
Get-Content "db/migrations/Cap2/UC15_BCSC/001_fix_worker_role_and_seed_incidents.sql" | 
  & "C:\xampp\mysql\bin\mysql.exe" -h localhost -u root
```

## ✅ Verification Checklist

After running all CAP2 migrations:

- [ ] UC15_BCSC:
  - [ ] Worker role fixed (role='worker' not 'admin')
  - [ ] Worker users exist (user_id=7, 12)
  - [ ] Test incidents seeded (6+ records)

## 📊 Migration Status

| Module | Status | Migrations | Last Updated |
|--------|--------|-----------|--------------|
| UC15_BCSC | ✅ Ready | 2 (fix_worker_role, create_incident_table) | 2025-11-30 |
| (Next module) | ⏳ TBD | - | - |

## 🎯 Future Modules

As CAP2 development progresses, add new modules:

```
Cap2/
├── UC15_BCSC/          ← Done
├── UC15_XXXX/          ← Coming soon
├── UC15_YYYY/          ← Coming soon
└── README.md
```

## 📝 Naming Convention

**Folder Structure:**
```
Cap2/UC{number}_{module_name}/
```

**Migration File Format:**
```
00{X}_descriptive_name.sql
```

Examples:
- `001_create_tables.sql`
- `002_add_foreign_keys.sql`
- `003_seed_initial_data.sql`

## 🔄 Migration Order

Migrations run in **alphabetical order** by default. Follow this pattern:

1. **001** - Create tables / Initial setup
2. **002** - Add relationships / Foreign keys
3. **003** - Seed data
4. **004+** - Updates / Fixes

## 🐛 Troubleshooting

**Issue:** Migration fails across modules
- Solution: Check CAP2 dependencies (e.g., UC15_BCSC needs user table from CAP1)
- Ensure CAP1 migrations have run first

**Issue:** Foreign key constraint errors
- Solution: Run migrations in the correct order
- Check: Parent tables/records exist in database

**Issue:** Duplicate data on re-run
- All CAP2 migrations are idempotent (safe to run multiple times)
- Check migration file for `INSERT IGNORE` or `WHERE NOT EXISTS`

## 📚 Related Documentation

- Parent: `db/migrations/README.md` - Complete migration overview
- Core migrations: `db/migrations/001-006_*.sql` - CAP1 RBAC system
- Individual module: Each module has its own `README.md`

## 💡 Adding New Module

To add a new CAP2 module:

1. Create folder: `Cap2/UC{number}_{name}/`
2. Create `README.md` in the module folder
3. Create migration files: `001_*.sql`, `002_*.sql`, etc.
4. Update this file with module info
5. Test all migrations

Example:
```powershell
mkdir "db/migrations/Cap2/UC15_XXXX"
New-Item "db/migrations/Cap2/UC15_XXXX/README.md"
New-Item "db/migrations/Cap2/UC15_XXXX/001_create_tables.sql"
```

---

**Created:** 2025-11-30  
**Version:** 1.0  
**Status:** Active Development  
**Module Count:** 1 (UC15_BCSC)
