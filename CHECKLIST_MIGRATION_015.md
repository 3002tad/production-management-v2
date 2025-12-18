# Checklist: Migration 015 Post-Implementation

**Status:** ✅ All core implementation completed  
**Date:** 2025-01-XX  
**Next Action:** Test in browser

---

## ✅ Completed Tasks

### Database
- [x] Migration 015 executed successfully
- [x] `production_lines.line_type` field added
- [x] `production_lines.is_primary` field added
- [x] `machines.machine_role` field added
- [x] Sample data updated (ZONE_A active, LINE01/LINE02 primary, LINE_VIRTUAL_01 created)
- [x] Machines 11-15 marked as backup

### Backend (Models & Controllers)
- [x] `MachineModel.getMachinesGrouped()` updated to select line_type and is_primary
- [x] `Machine.store()` controller validates and saves machine_role
- [x] `Machine.update()` controller validates and updates machine_role
- [x] machine_role validation: only 'primary' or 'backup' accepted, defaults to 'primary'

### Frontend Views
- [x] **Machine Index**:
  - [x] Machine cards show "BACKUP" badge for backup machines
  - [x] Production lines show line_type badges (blue/green/gray)
  - [x] Primary lines show lock icon 🔒
  - [x] New tab "Máy Dự Phòng" added with backup machines list
  - [x] Backup tab shows statistics (active vs total)
  - [x] Backup machines have yellow/orange visual styling
- [x] **Machine Create Form**:
  - [x] "Vai trò máy" dropdown added after status field
  - [x] Default value: "Máy chính (Primary)"
  - [x] Help text explaining primary vs backup
- [x] **Machine Edit Form**:
  - [x] "Vai trò máy" dropdown added with current value selected
  - [x] Layout adjusted to accommodate new field

### Documentation
- [x] `MIGRATION_015_IMPLEMENTATION_SUMMARY.md` created with complete details
- [x] `MACHINES_MODULE_STRUCTURE.md` already exists from previous session

---

## 🧪 Testing Required (Manual)

### 1. Machine List Page (`/leader/machine`)
```
Navigate to: http://localhost/leader/machine/

Expected Results:
□ Page loads without errors
□ Machine cards display correctly
□ Machines with machine_role='backup' show yellow "BACKUP" badge
□ Production lines show line_type badges:
  - LINE01: "Sản xuất thô" (blue)
  - LINE02: "Lắp ráp & QC" (green)
  - LINE_VIRTUAL_01: "Line ảo" (gray)
□ Primary lines (LINE01, LINE02) show lock icon 🔒
□ Tab "Máy Dự Phòng" appears between "Danh sách Máy" and "Quản lý Khu vực"
```

### 2. Backup Machines Tab
```
Click on: "Máy Dự Phòng" tab

Expected Results:
□ Tab switches without page reload
□ Shows statistics: "X máy sẵn sàng" and "Y tổng máy dự phòng"
□ Only backup machines displayed (machines 11-15 from migration sample data)
□ Backup machine cards have:
  - Orange/yellow background
  - Shield icon
  - "BACKUP" badge on machine code
□ If no backup machines: Shows empty state with shield icon
```

### 3. Create New Machine (`/leader/machine/create`)
```
Navigate to: http://localhost/leader/machine/create

Expected Results:
□ Form loads successfully
□ "Vai trò máy" dropdown appears after "Trạng thái" field
□ Dropdown shows two options:
  - "Máy chính (Primary)" - selected by default
  - "Máy dự phòng (Backup)"
□ Help text displayed: "Máy chính: sử dụng thường xuyên. Máy dự phòng: thay thế khi máy chính gặp sự cố."
□ Can select "Máy dự phòng (Backup)" option
□ Form submits successfully with machine_role value
```

**Test Case:**
1. Fill form:
   - Code: TEST_BACKUP_001
   - Name: Test Backup Machine
   - Capacity: 100
   - Stage Type: molding
   - Status: active
   - **Machine Role: Máy dự phòng (Backup)** ← Select this
   - Line: Any line
2. Click "Lưu máy mới"
3. Check results:
   □ Redirected to machine index
   □ Success message displayed
   □ New machine appears in "Danh sách Máy" with yellow "BACKUP" badge
   □ New machine also appears in "Máy Dự Phòng" tab

### 4. Edit Existing Machine (`/leader/machine/edit/{id}`)
```
Navigate to: Edit any existing machine

Expected Results:
□ Form loads with current machine data
□ "Vai trò máy" dropdown appears
□ Current machine_role value is pre-selected:
  - If machine_role='primary' or NULL: "Máy chính (Primary)" selected
  - If machine_role='backup': "Máy dự phòng (Backup)" selected
□ Can change machine_role value
□ Form submits successfully
□ Updated role persists and shows in machine index
```

**Test Case:**
1. Edit a primary machine (id 1-10)
2. Change "Vai trò máy" to "Máy dự phòng (Backup)"
3. Click "Lưu"
4. Check results:
   □ Redirected to detail page
   □ Success message displayed
   □ Machine now shows "BACKUP" badge in index
   □ Machine now appears in "Máy Dự Phòng" tab

### 5. Browser Console Check
```
Open Developer Tools (F12) > Console tab

Expected Results:
□ No JavaScript errors
□ No PHP warnings/errors
□ No 404 errors for assets
□ No SQL errors in network tab
```

---

## 🔍 SQL Verification Queries

Run these queries in phpMyAdmin or MySQL client to verify data:

### Check line_type and is_primary fields exist
```sql
DESCRIBE production_lines;

-- Should show:
-- line_type     enum('production_raw','assembly_qc','virtual')  'production_raw'
-- is_primary    tinyint(1)                                      0
```

### Check machine_role field exists
```sql
DESCRIBE machines;

-- Should show:
-- machine_role  enum('primary','backup')  'primary'
```

### Check sample data updates
```sql
-- Check zones
SELECT zone_id, zone_code, zone_name, status FROM zones;
-- Expected: ZONE_A status=1, ZONE_B/C status=0

-- Check production lines
SELECT id, line_code, line_name, line_type, is_primary, zone_id, status 
FROM production_lines;
-- Expected:
-- LINE01: production_raw, is_primary=1
-- LINE02: assembly_qc, is_primary=1
-- LINE_VIRTUAL_01: virtual, is_primary=0

-- Check backup machines
SELECT id, code, name, machine_role, status, line_id 
FROM machines 
WHERE machine_role = 'backup';
-- Expected: Machines with id 11-15 (or as updated by migration)

-- Count machines by role
SELECT machine_role, COUNT(*) as count 
FROM machines 
GROUP BY machine_role;
-- Expected: 
-- primary: X machines
-- backup: 5 machines (from sample data)
```

---

## ⚠️ Known Limitations

### Line Deletion Protection
**Status:** UI indicator ready, but no deletion endpoint exists yet

- Primary lines show lock icon 🔒 in UI
- No line deletion functionality currently implemented in codebase
- When implementing line deletion in future:
  ```php
  if ($line->is_primary == 1) {
      return error('Không thể xóa dây chuyền chính');
  }
  ```

### Virtual Line Workflow
**Status:** Database structure ready, business logic not implemented

- Virtual lines can be created (line_type='virtual')
- No automatic workflows yet for:
  - Assigning backup machines to virtual line during capacity scaling
  - Suggesting virtual line when primary lines at full capacity
  - Tracking virtual line usage analytics

### Machine Replacement Suggestions
**Status:** Data available, UI not implemented

- Database can identify backup machines by role
- No UI for suggesting backup when primary machine breaks
- Feature can be added in shift creation/incident reporting modules

---

## 📝 Quick Reference

### Machine Role Values
- `primary` = Máy chính - Regular production machines
- `backup` = Máy dự phòng - Replacement machines

### Line Type Values
- `production_raw` = Sản xuất thô - Raw production line (blue badge)
- `assembly_qc` = Lắp ráp & QC - Assembly and quality control line (green badge)
- `virtual` = Line ảo - Virtual/flexible line (gray badge)

### Primary Line Protection
- `is_primary=1` = Primary line - Cannot be deleted (shows lock icon 🔒)
- `is_primary=0` = Secondary line - Can be deleted

---

## 🎯 Success Criteria

Implementation is considered successful if:

- ✅ All 5 updated files load without PHP/SQL errors
- ✅ Machine index page displays correctly with new badges
- ✅ "Máy Dự Phòng" tab shows only backup machines
- ✅ Create machine form includes machine_role field
- ✅ Edit machine form shows current machine_role value
- ✅ New machines save with correct machine_role
- ✅ Updated machines persist machine_role changes
- ✅ Line type badges display on production lines
- ✅ Primary lines show lock icon

---

## 🚀 Next Steps After Testing

### If Everything Works:
1. ✅ Mark migration 015 as production-ready
2. Create git commit with message:
   ```
   feat(machines): Implement Zone-Line-Machine hierarchy with primary/backup classification
   
   - Add machine_role field (primary/backup)
   - Add line_type field (production_raw/assembly_qc/virtual)
   - Add is_primary field for line protection
   - Create backup machines tab in UI
   - Update machine create/edit forms
   - Update MachineModel to load new fields
   
   Migration: 015_restructure_zones_lines_machines.sql
   Refs: MACHINES_MODULE_STRUCTURE.md, MIGRATION_015_IMPLEMENTATION_SUMMARY.md
   ```
3. Update project documentation with new module structure
4. Plan next features (machine replacement workflow, virtual line management)

### If Issues Found:
1. Document specific errors in conversation
2. Check PHP error logs: `application/logs/`
3. Check browser console for JavaScript errors
4. Verify database migration ran correctly
5. Check file permissions if assets not loading

---

## 📞 Support Resources

- **Migration Script:** `db/migrations/Cap2/CaLamViec/015_restructure_zones_lines_machines.sql`
- **Documentation:** `MACHINES_MODULE_STRUCTURE.md`
- **Implementation Summary:** `MIGRATION_015_IMPLEMENTATION_SUMMARY.md`
- **Original Session Logs:** (if kept in project notes)

---

**Status:** Ready for browser testing 🎉
**Estimated Testing Time:** 15-20 minutes
**Risk Level:** Low (no breaking changes, backwards compatible)
