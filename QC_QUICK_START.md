# QC Module - Quick Start Guide

## 🚀 5-Minute Setup

### 1. Run Migrations
```sql
-- In phpMyAdmin, execute:
SOURCE d:/Code/PTUD/production-management-v2/db/migrations/007_create_qc_module_tables.sql;
SOURCE d:/Code/PTUD/production-management-v2/db/seeds/qc_module_seed_data.sql;
```

### 2. Create Upload Folder
```powershell
# In terminal at project root:
mkdir uploads\qc
```

### 3. Test Login
- URL: `http://localhost:8080/production-management-v2/qc/`
- Username: `qc_inspector`
- Password: `password`

## 📋 Quick Demo Flow

1. **View Pending** → See 2 pending closures
2. **Click "Inspect"** on first closure → Creates session
3. **Fill Checklist** → Select PASS/FAIL for each item
4. **Save Checklist** → See recommendation
5. **Upload Photo** → Add evidence (optional)
6. **Click APPROVE** → Decision recorded ✓

## 🎯 Key URLs

| URL | Description |
|-----|-------------|
| `/qc/` | Pending closures list |
| `/qc/sessions/{id}` | Inspection session |
| `/qc/adjustments` | Rejected items |

## 🧪 Test Scenarios

### ✅ Test APPROVE
- Closure: `SC-20251102-LINE01-CA1`
- Fill all PASS → Click APPROVE
- Expected: Status → VERIFIED

### ❌ Test REJECT
- Create new session
- Fill some FAIL items
- Upload 1 photo
- Click REJECT → Enter reason
- Expected: Adjustment request created

### ⚠️ Test Near-Threshold
- Fill checklist with 5% defect rate
- Try to decide
- Expected: Warning to increase sample

## 📊 Sample Data Summary

**Users:**
- `qc_inspector` / `password` (QC role)

**Closures:**
- 2x PENDING_QC (ready to inspect)
- 1x VERIFIED (already approved)
- 1x REJECTED (with adjustment request)

**Checklists:**
- 5 items for PROD-BP-001
- 4 items for PROD-BP-002

## 🔧 Configuration

Default AQL: **2.5%**  
Near-Threshold Margin: **5%**  
Max Upload: **10MB**

**Change AQL:**
```sql
UPDATE qc_config SET config_value = '3.0' WHERE config_key = 'QC_AQL_DEFAULT';
```

## 🐛 Common Issues

| Issue | Fix |
|-------|-----|
| No checklist | Add items to `qc_checklist_master` |
| Upload fails | Check `uploads/qc/` exists & writable |
| 409 error | Session already decided (locked) |
| 403 error | Need QC role |

## 📖 Full Documentation

See: `QC_MODULE_README.md` for complete details.

---
**Ready to test?** → Login as `qc_inspector` and start inspecting! 🔬
