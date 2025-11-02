# QC MODULE - DATABASE & CODE AUDIT REPORT

**Date**: 2025-11-02  
**Audited by**: AI Assistant  
**Purpose**: Verify database structure vs code implementation

---

## ❌ CRITICAL ISSUES FOUND

### 1. TABLE NAME MISMATCH: `users` vs `user`

**Location**: `application/models/QcModel.php` lines 152, 168, 520

**Problem**:
```php
// In QcModel.php - WRONG
$this->db->join('users u', 'qs.inspector_code = u.username', 'left');  // Line 152
$this->db->join('users u', 'qs.inspector_code = u.username', 'left');  // Line 168
$this->db->join('users u', 'ar.created_by = u.username', 'left');      // Line 520
```

**Actual Table Name**: `user` (singular, from RBAC migration)

**Impact**: 
- ❌ JOIN queries will FAIL with "Table 'users' doesn't exist"
- ❌ QC session list won't load inspector names
- ❌ Adjustment requests won't show creator names

**Fix Required**:
```php
// CORRECT - Change to singular
$this->db->join('user u', 'qs.inspector_code = u.username', 'left');
```

---

### 2. USER CREDENTIALS MISMATCH

**In seed_roles_data (002_seed_roles_data.sql)**:
```sql
-- Correct user in RBAC migration
INSERT INTO `user` (`username`, `password`, `role_id`, `full_name`, `email`, `is_active`)
SELECT 'qc', 'qc123', 5, 'Phạm Văn D - Nhân viên QC', 'qc@company.com', 1
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `username` = 'qc');
```

**In QC seed data (qc_module_seed_data.sql)**:
```sql
-- DIFFERENT user - creates confusion
INSERT INTO `user` (`username`, `password`, `full_name`, `email`, `phone`, `role_id`, `is_active`)
SELECT 'qc_inspector', 'password', 'QC Inspector', 'qc@production.com', '0987654321', 5, 1
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `username` = 'qc_inspector');
```

**Problem**: Two different QC users created:
1. `qc` / `qc123` (from RBAC migration)
2. `qc_inspector` / `password` (from QC seed data)

**Impact**:
- Confusion about which user to use for testing
- Sample QC sessions reference `qc_inspector`
- Documentation says use `qc` / `qc123`

**Recommendation**: 
- **Use `qc` / `qc123`** (already in RBAC)
- Remove `qc_inspector` INSERT from qc_module_seed_data.sql
- Update sample sessions to use `inspector_code = 'qc'`

---

## ✅ TABLE STRUCTURE VERIFICATION

### Tables Created in Migration 007

| Table Name | Status | Columns | Indexes | Foreign Keys |
|------------|--------|---------|---------|--------------|
| `shift_closures` | ✅ OK | 16 | 3 | 0 |
| `qc_sessions` | ✅ OK | 8 | 2 | 1 (closure_id) |
| `qc_items` | ✅ OK | 11 | 2 | 1 (session_id) |
| `qc_decisions` | ✅ OK | 9 | 2 | 1 (session_id) |
| `qc_attachments` | ✅ OK | 8 | 1 | 1 (session_id) |
| `adjustment_requests` | ✅ OK | 11 | 2 | 1 (closure_id) |
| `qc_checklist_master` | ✅ OK | 12 | 2 | 0 |
| `qc_config` | ✅ OK | 4 | 1 (unique key) | 0 |

**Total**: 8 tables, all with proper charset `utf8mb4_unicode_ci`

---

## 📊 FIELD MAPPING: DATABASE vs CODE

### shift_closures

**Database Schema** (007_create_qc_module_tables.sql):
```sql
CREATE TABLE `shift_closures` (
  `id` INT UNSIGNED PRIMARY KEY,
  `code` VARCHAR(50) UNIQUE,
  `line_code` VARCHAR(20),
  `shift_code` VARCHAR(10),
  `project_code` VARCHAR(50),
  `lot_code` VARCHAR(50),
  `product_code` VARCHAR(50),
  `variant` VARCHAR(50),
  `qty_finished` INT UNSIGNED DEFAULT 0,
  `qty_waste` INT UNSIGNED DEFAULT 0,
  `status` ENUM('PENDING_QC', 'VERIFIED', 'REJECTED'),
  `can_receive_fg` TINYINT(1) DEFAULT 0,
  `closed_at` DATETIME,
  `closed_by` VARCHAR(50),
  `created_at` TIMESTAMP,
  `updated_at` TIMESTAMP
);
```

**Code Usage** (QcModel.php):
```php
// SELECT fields used in getPendingClosures()
'sc.id, sc.code, sc.line_code, sc.shift_code, sc.project_code, sc.lot_code, 
 sc.product_code, sc.variant, sc.qty_finished, sc.qty_waste, 
 sc.status, sc.closed_at, sc.closed_by,
 p.name as project_name,
 pr.name as product_name'

// UPDATE fields in updateClosureStatus()
'status' => $status,
'can_receive_fg' => $can_receive_fg

// JOIN to project, product (LEFT JOIN - OK)
```

✅ **Status**: All fields match

---

### qc_sessions

**Database Schema**:
```sql
CREATE TABLE `qc_sessions` (
  `id` INT UNSIGNED PRIMARY KEY,
  `code` VARCHAR(50) UNIQUE,
  `closure_id` INT UNSIGNED,
  `inspector_code` VARCHAR(50),
  `inspector_name` VARCHAR(100),
  `started_at` DATETIME,
  `status` ENUM('OPEN', 'DECIDED'),
  `created_at` TIMESTAMP,
  `updated_at` TIMESTAMP,
  FOREIGN KEY (closure_id) REFERENCES shift_closures(id)
);
```

**Code Usage**:
```php
// SELECT in getSessionsByInspector()
'qs.id, qs.code, qs.closure_id, qs.inspector_code, qs.inspector_name,
 qs.started_at, qs.status,
 sc.code as closure_code, sc.line_code, sc.product_code,
 u.full_name as inspector_full_name'  // ❌ WRONG: users → user

// JOIN - ❌ ERROR
$this->db->join('users u', 'qs.inspector_code = u.username', 'left');
// Should be: 'user u'
```

❌ **Status**: JOIN error with `users` table

---

### qc_items

**Database Schema**:
```sql
CREATE TABLE `qc_items` (
  `id` INT UNSIGNED PRIMARY KEY,
  `session_id` INT UNSIGNED,
  `checklist_item_code` VARCHAR(50),
  `checklist_item_name` VARCHAR(200),
  `measure_value` DECIMAL(10,2),
  `defect_code` VARCHAR(50),
  `defect_count` INT UNSIGNED DEFAULT 0,
  `severity` ENUM('MINOR', 'MAJOR', 'CRITICAL'),
  `result` ENUM('PASS', 'FAIL'),
  `note` TEXT,
  `created_at` TIMESTAMP,
  `updated_at` TIMESTAMP
);
```

**Code Usage** (View: session_v2.php):
```php
// Form fields expect:
results[<?= $item->id ?>]     → maps to qc_items.result
defects[<?= $item->id ?>]     → maps to qc_items.defect_count
severity[<?= $item->id ?>]    → maps to qc_items.severity
notes[<?= $item->id ?>]       → maps to qc_items.note (singular)
```

✅ **Status**: All fields match (note: `note` is singular in DB, `notes` in form name - OK)

---

### qc_decisions

**Database Schema**:
```sql
CREATE TABLE `qc_decisions` (
  `id` INT UNSIGNED PRIMARY KEY,
  `session_id` INT UNSIGNED UNIQUE,
  `result` ENUM('APPROVE', 'REJECT'),
  `aql` DECIMAL(5,2),
  `defect_rate` DECIMAL(5,2),
  `reason` TEXT,
  `decided_at` DATETIME,
  `decided_by` VARCHAR(50)
);
```

**Code Usage**:
```php
// INSERT in makeApproveDecision()
$decision_data = [
    'session_id' => $session_id,
    'result' => 'APPROVE',
    'aql' => $aql,
    'defect_rate' => $defect_rate,
    'reason' => $notes,         // Optional for APPROVE
    'decided_at' => date('Y-m-d H:i:s'),
    'decided_by' => $this->session->userdata('username')
];
```

✅ **Status**: All fields match

---

### qc_attachments

**Database Schema**:
```sql
CREATE TABLE `qc_attachments` (
  `id` INT UNSIGNED PRIMARY KEY,
  `session_id` INT UNSIGNED,
  `filename` VARCHAR(255),
  `path` VARCHAR(500),
  `mime_type` VARCHAR(100),
  `file_size` INT UNSIGNED,
  `uploaded_by` VARCHAR(50),
  `created_at` TIMESTAMP
);
```

**Code Usage** (View expects):
```php
// In session_v2.php - displays attachments
$att->file_path   // ❌ WRONG: should be $att->path
$att->file_type   // ❌ WRONG: should be $att->mime_type
```

**View Code**:
```html
<img src="<?= site_url('uploads/qc/' . $att->file_path) ?>" />  <!-- WRONG -->
<p><?= $att->file_type ?></p>  <!-- WRONG -->
```

❌ **Status**: Field name mismatch in view

**Fix Required**:
```php
// Correct field names
<img src="<?= site_url('uploads/qc/' . $att->path) ?>" />
<p><?= $att->mime_type ?></p>
```

---

### adjustment_requests

**Database Schema**:
```sql
CREATE TABLE `adjustment_requests` (
  `id` INT UNSIGNED PRIMARY KEY,
  `code` VARCHAR(50) UNIQUE,
  `closure_id` INT UNSIGNED,
  `created_by` VARCHAR(50),
  `assigned_to` VARCHAR(50),
  `reason` TEXT,
  `status` ENUM('OPEN', 'ACKED', 'DONE'),
  `created_at` TIMESTAMP,
  `updated_at` TIMESTAMP,
  `acknowledged_at` DATETIME,
  `completed_at` DATETIME
);
```

**Code Usage**:
```php
// SELECT in getAdjustmentRequestsByStatus()
'ar.id, ar.code, ar.closure_id, ar.created_by, ar.assigned_to,
 ar.reason, ar.status, ar.created_at, ar.acknowledged_at,
 sc.code as closure_code, sc.line_code,
 u.full_name as creator_name'  // ❌ WRONG: users → user
```

❌ **Status**: JOIN error with `users` table

---

### qc_checklist_master

**Database Schema**:
```sql
CREATE TABLE `qc_checklist_master` (
  `id` INT UNSIGNED PRIMARY KEY,
  `code` VARCHAR(50) UNIQUE,
  `product_code` VARCHAR(50),
  `variant` VARCHAR(50),
  `item_name` VARCHAR(200),
  `criteria` TEXT,
  `sample_size` INT UNSIGNED,
  `aql` DECIMAL(5,2) DEFAULT 2.5,
  `category` VARCHAR(50),
  `sequence` INT UNSIGNED DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP,
  `updated_at` TIMESTAMP
);
```

**Code Usage** (View expects):
```php
// session_v2.php expects from ChecklistService
$item->criteria_name    // ❌ WRONG: DB has item_name
$item->description      // ❌ WRONG: DB has criteria
$item->test_method      // ❌ WRONG: Not in DB schema
```

**ChecklistService.php returns**:
```php
// getChecklist() method should map:
'criteria_name' => $row->item_name,     // Map item_name to criteria_name
'description' => $row->criteria,        // Map criteria to description
'test_method' => null,                  // Not available in current schema
'item_code' => $row->code
```

⚠️ **Status**: Field mapping needed in service layer

---

## 🔍 COMPLETE ISSUE LIST

### Critical (Must Fix Before Testing)

1. **QcModel.php - Line 152, 168, 520**: 
   - Change `'users u'` → `'user u'`
   - Affects: session list, adjustment requests

2. **session_v2.php - Attachment display**:
   - Change `$att->file_path` → `$att->path`
   - Change `$att->file_type` → `$att->mime_type`

3. **ChecklistService.php**:
   - Map `item_name` to `criteria_name`
   - Map `criteria` to `description`
   - Add `test_method` field to qc_checklist_master OR set null in service

### High Priority (Data Consistency)

4. **qc_module_seed_data.sql**:
   - Remove `qc_inspector` user INSERT (conflicts with RBAC)
   - Update sample sessions to use `inspector_code = 'qc'`
   - Use consistent user: `qc` / `qc123`

### Medium Priority (Enhancements)

5. **Add `test_method` column** to qc_checklist_master:
   ```sql
   ALTER TABLE qc_checklist_master 
   ADD COLUMN test_method VARCHAR(200) NULL AFTER criteria;
   ```

6. **Missing upload directory**: Create `uploads/qc/` with write permissions

7. **Password hashing**: Seed data uses plaintext passwords
   - Change to bcrypt in production
   - Current: `password`, `qc123` are plaintext

---

## 🛠️ RECOMMENDED FIXES

### Fix 1: Update QcModel.php

**File**: `application/models/QcModel.php`

**Line 152** (in `getSessionsByInspector`):
```php
// BEFORE
$this->db->join('users u', 'qs.inspector_code = u.username', 'left');

// AFTER
$this->db->join('user u', 'qs.inspector_code = u.username', 'left');
```

**Line 168** (in `getSessionById`):
```php
// BEFORE
$this->db->join('users u', 'qs.inspector_code = u.username', 'left');

// AFTER
$this->db->join('user u', 'qs.inspector_code = u.username', 'left');
```

**Line 520** (in `getAdjustmentRequestsByStatus`):
```php
// BEFORE
$this->db->join('users u', 'ar.created_by = u.username', 'left');

// AFTER
$this->db->join('user u', 'ar.created_by = u.username', 'left');
```

---

### Fix 2: Update session_v2.php

**File**: `application/views/qc/session_v2.php`

**Around line 350** (attachment display):
```php
// BEFORE
<img src="<?= site_url('uploads/qc/' . $att->file_path) ?>" />
<p class="text-xs mb-0"><?= $att->file_type ?></p>

// AFTER
<img src="<?= site_url('uploads/qc/' . $att->path) ?>" />
<p class="text-xs mb-0"><?= $att->mime_type ?></p>
```

---

### Fix 3: Update ChecklistService.php

**File**: `application/libraries/ChecklistService.php`

In `getChecklist()` method, map fields correctly:
```php
public function getChecklist($product_code, $variant = null)
{
    $this->CI->load->model('QcModel');
    $master_items = $this->CI->QcModel->getChecklistMaster($product_code, $variant);
    
    $checklist = [];
    foreach ($master_items as $item) {
        $checklist[] = (object)[
            'item_code' => $item->code,
            'criteria_name' => $item->item_name,      // Map item_name
            'description' => $item->criteria,         // Map criteria
            'test_method' => null,                    // Not in DB yet
            'sample_size' => $item->sample_size,
            'aql' => $item->aql,
            'category' => $item->category,
            'sequence' => $item->sequence
        ];
    }
    
    return $checklist;
}
```

---

### Fix 4: Clean up seed data

**File**: `db/qc/qc_module_seed_data.sql`

**Remove lines 10-14** (qc_inspector user):
```sql
-- DELETE THIS BLOCK (conflicts with RBAC user 'qc')
-- INSERT INTO `user` (`username`, `password`, `full_name`, `email`, `phone`, `role_id`, `is_active`)
-- SELECT 'qc_inspector', 'password', 'QC Inspector', 'qc@production.com', '0987654321', 5, 1
-- WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `username` = 'qc_inspector');
```

**Update line 36** (sample session):
```sql
-- BEFORE
'qc_inspector', 
'QC Inspector', 

-- AFTER
'qc', 
'Phạm Văn D - Nhân viên QC',
```

**Update all other references** to `qc_inspector` → `qc` in:
- Line 55 (completed session)
- Line 82 (rejected session)
- Line 108 (decision decided_by)
- Line 127 (adjustment request)

---

### Fix 5: Add test_method column (Optional but recommended)

**New migration file**: `db/migrations/008_add_test_method_to_checklist.sql`

```sql
-- Add test_method column to qc_checklist_master
ALTER TABLE qc_checklist_master 
ADD COLUMN test_method VARCHAR(200) NULL 
COMMENT 'Test/inspection method description'
AFTER criteria;

-- Update existing records with sample test methods
UPDATE qc_checklist_master 
SET test_method = 'Visual inspection with magnifier'
WHERE category = 'visual';

UPDATE qc_checklist_master 
SET test_method = 'Vernier caliper measurement'
WHERE category = 'dimensional';

UPDATE qc_checklist_master 
SET test_method = 'Functional testing per spec'
WHERE category = 'functional';
```

---

## ✅ POST-FIX VERIFICATION STEPS

1. **Fix all code issues** (QcModel, session_v2, ChecklistService)

2. **Run migrations**:
   ```bash
   mysql -u root -p production_db < db/qc/007_create_qc_module_tables.sql
   mysql -u root -p production_db < db/qc/qc_module_seed_data.sql
   ```

3. **Test login**:
   ```
   Username: qc
   Password: qc123
   ```

4. **Test QC flow**:
   - Visit http://localhost/qc/
   - Should see pending closures
   - Click "Kiểm tra" → Creates session
   - Fill checklist → See AI recommendation
   - Test APPROVE/REJECT

5. **Verify queries**:
   ```sql
   -- Check user exists
   SELECT username, full_name, role_id FROM user WHERE username = 'qc';
   
   -- Check sessions load inspector names
   SELECT qs.code, qs.inspector_code, u.full_name 
   FROM qc_sessions qs 
   LEFT JOIN user u ON qs.inspector_code = u.username;
   
   -- Check attachments
   SELECT id, session_id, filename, path, mime_type 
   FROM qc_attachments;
   
   -- Check checklist master
   SELECT code, product_code, item_name, criteria 
   FROM qc_checklist_master;
   ```

---

## 📈 SUMMARY

**Total Issues Found**: 7

| Priority | Count | Status |
|----------|-------|--------|
| Critical | 3 | ❌ Blocking |
| High | 1 | ⚠️ Important |
| Medium | 3 | 📝 Enhancement |

**Estimated Fix Time**: 30 minutes

**Testing Impact**: 
- ❌ Current code will FAIL on QC session list (users table not found)
- ❌ Attachment display will show empty images
- ⚠️ Checklist display will miss test_method field

**After Fixes**:
- ✅ All JOINs will work correctly
- ✅ User `qc` / `qc123` will be consistent
- ✅ Attachments will display properly
- ✅ Checklist will show all required fields

---

## 🎯 ACTION ITEMS

**For Developer**:
1. [ ] Apply Fix 1: QcModel.php (3 lines)
2. [ ] Apply Fix 2: session_v2.php (2 lines)
3. [ ] Apply Fix 3: ChecklistService.php (field mapping)
4. [ ] Apply Fix 4: qc_module_seed_data.sql (remove qc_inspector)
5. [ ] Optional: Apply Fix 5 (add test_method column)
6. [ ] Test complete QC flow
7. [ ] Update documentation with correct credentials

**Testing Checklist**:
- [ ] Login with `qc` / `qc123` works
- [ ] Pending closures list loads
- [ ] Session creation works
- [ ] Checklist displays correctly
- [ ] File upload works
- [ ] Attachments display with correct path
- [ ] AI recommendation shows
- [ ] APPROVE flow completes
- [ ] REJECT validation works
- [ ] Adjustment request created

---

**END OF AUDIT REPORT**
