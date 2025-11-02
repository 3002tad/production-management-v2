# QC Module - Deployment Checklist

## ✅ Pre-Deployment

- [ ] **Backup Database**
  ```bash
  mysqldump -u root -p production_db > backup_$(date +%Y%m%d).sql
  ```

- [ ] **Review Code**
  - [ ] Check all files are committed
  - [ ] Review security settings
  - [ ] Verify base_url in config.php

- [ ] **Environment Check**
  - [ ] PHP version ≥ 7.2
  - [ ] CodeIgniter 3.x installed
  - [ ] MySQL/MariaDB running
  - [ ] GD library enabled (for image uploads)

## 📦 Deployment Steps

### Step 1: Database Migration

- [ ] **Run Migration 007**
  ```sql
  SOURCE db/migrations/007_create_qc_module_tables.sql;
  ```

- [ ] **Verify Tables Created**
  ```sql
  SHOW TABLES LIKE '%qc%';
  SHOW TABLES LIKE 'shift_closures';
  SHOW TABLES LIKE 'adjustment_requests';
  ```
  Expected: 8 tables

- [ ] **Check Indexes**
  ```sql
  SHOW INDEX FROM shift_closures;
  SHOW INDEX FROM qc_sessions;
  SHOW INDEX FROM qc_items;
  ```

### Step 2: Seed Data (Testing Only)

- [ ] **Insert Sample Data**
  ```sql
  SOURCE db/seeds/qc_module_seed_data.sql;
  ```

- [ ] **Verify Seed Data**
  ```sql
  SELECT COUNT(*) FROM shift_closures;      -- Should be 4
  SELECT COUNT(*) FROM qc_checklist_master; -- Should be 9
  SELECT COUNT(*) FROM qc_sessions;         -- Should be 3
  ```

### Step 3: File System Setup

- [ ] **Create Upload Directory**
  ```bash
  # Windows
  mkdir uploads\qc
  
  # Linux/Mac
  mkdir -p uploads/qc
  chmod 755 uploads/qc
  ```

- [ ] **Verify Permissions**
  ```bash
  # Test write permission
  touch uploads/qc/test.txt
  rm uploads/qc/test.txt
  ```

### Step 4: Code Deployment

- [ ] **Copy Files to Server**
  ```
  application/controllers/Qc.php
  application/models/QcModel.php
  application/libraries/ChecklistService.php
  application/views/qc/pending.php
  application/views/qc/session.php
  application/views/qc/adjustments.php
  ```

- [ ] **Verify Files Exist**
  ```bash
  ls application/controllers/Qc.php
  ls application/models/QcModel.php
  ls application/libraries/ChecklistService.php
  ls -la application/views/qc/
  ```

### Step 5: Configuration

- [ ] **Check Config**
  ```php
  // In application/config/config.php
  $config['base_url'] = 'http://localhost:8080/production-management-v2/';
  ```

- [ ] **Verify Upload Settings**
  ```php
  // In application/config/config.php or Qc controller
  Max upload: 10MB
  Allowed types: jpg, jpeg, png, gif, mp4, mov
  ```

- [ ] **Check Database Connection**
  ```php
  // In application/config/database.php
  hostname, username, password, database
  ```

### Step 6: User Setup

- [ ] **Create QC User** (if not using seed)
  ```sql
  INSERT INTO users (username, password, full_name, email, role_id, is_active)
  VALUES ('qc_inspector', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
          'QC Inspector', 'qc@company.com', 5, 1);
  -- Password: password
  ```

- [ ] **Verify QC Role Exists**
  ```sql
  SELECT * FROM roles WHERE id = 5;  -- Should be qc_staff
  ```

## 🧪 Testing Phase

### Test 1: Basic Access

- [ ] **Login as QC User**
  - URL: `http://localhost:8080/production-management-v2/qc/`
  - Username: `qc_inspector`
  - Password: `password`

- [ ] **Verify Pending Page Loads**
  - [ ] No PHP errors
  - [ ] Closures table visible
  - [ ] Filters working

### Test 2: APPROVE Workflow

- [ ] **Create Session**
  - [ ] Click "Inspect" button
  - [ ] Session created successfully
  - [ ] Checklist loads

- [ ] **Fill Checklist**
  - [ ] Select all PASS results
  - [ ] Save checklist
  - [ ] See recommendation: APPROVE

- [ ] **Make Decision**
  - [ ] Click APPROVE button
  - [ ] Confirmation works
  - [ ] Success message shown
  - [ ] Session status = DECIDED

- [ ] **Verify Database**
  ```sql
  SELECT status FROM qc_sessions WHERE id = LAST_INSERT_ID();  -- DECIDED
  SELECT status FROM shift_closures WHERE id = <closure_id>;   -- VERIFIED
  SELECT can_receive_fg FROM shift_closures WHERE id = <closure_id>; -- 1
  ```

### Test 3: REJECT Workflow

- [ ] **Create New Session**
- [ ] **Fill Checklist with FAIL items**
- [ ] **Upload Photo**
  - [ ] File upload works
  - [ ] Preview shows
  - [ ] File saved to uploads/qc/

- [ ] **Click REJECT**
  - [ ] Modal opens
  - [ ] Enter reason
  - [ ] Confirm reject

- [ ] **Verify Adjustment Request Created**
  ```sql
  SELECT * FROM adjustment_requests WHERE closure_id = <closure_id>;
  ```

### Test 4: Validation

- [ ] **Try REJECT without reason**
  - [ ] Error: "Reason required"

- [ ] **Try REJECT without attachment**
  - [ ] Error: "Attachment required"

- [ ] **Try to edit decided session**
  - [ ] Error: 409 "Session locked"

### Test 5: Near-Threshold

- [ ] **Create session with ~5% defect rate**
- [ ] **Try to decide**
  - [ ] Warning: "Increase sample size"
  - [ ] HTTP 409 returned

## 🔍 Post-Deployment Verification

### Database Check

- [ ] **Run Verification Queries**
  ```sql
  -- Check tables exist
  SELECT TABLE_NAME, TABLE_ROWS 
  FROM information_schema.TABLES 
  WHERE TABLE_SCHEMA = 'production_db' 
    AND TABLE_NAME LIKE '%qc%';
  
  -- Check config
  SELECT * FROM qc_config;
  
  -- Check closures
  SELECT code, status FROM shift_closures;
  ```

### Application Check

- [ ] **Check Logs**
  ```bash
  tail -f application/logs/log-$(date +%Y-%m-%d).php
  ```

- [ ] **Test All Routes**
  - [ ] GET /qc/
  - [ ] POST /qc/createSession
  - [ ] GET /qc/sessions/{id}
  - [ ] POST /qc/sessions/{id}/items
  - [ ] POST /qc/sessions/{id}/attachments
  - [ ] POST /qc/sessions/{id}/decision
  - [ ] GET /qc/adjustments

### Performance Check

- [ ] **Query Performance**
  ```sql
  EXPLAIN SELECT * FROM shift_closures WHERE status = 'PENDING_QC';
  -- Should use idx_status_line_shift index
  ```

- [ ] **Upload Speed**
  - [ ] Upload 5MB file < 3 seconds

## 🐛 Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| Tables not created | Re-run migration 007 |
| Upload fails | Check uploads/qc/ permissions |
| 404 on /qc/ | Verify Qc.php exists in controllers/ |
| "No checklist" error | Insert checklist items for product |
| Login fails | Check users table, verify password hash |
| 403 error | Check user has role_id = 5 |

### Reset Instructions

If you need to start over:

```sql
-- Drop all QC tables
DROP TABLE IF EXISTS adjustment_requests;
DROP TABLE IF EXISTS qc_attachments;
DROP TABLE IF EXISTS qc_decisions;
DROP TABLE IF EXISTS qc_items;
DROP TABLE IF EXISTS qc_sessions;
DROP TABLE IF EXISTS qc_checklist_master;
DROP TABLE IF EXISTS qc_config;
DROP TABLE IF EXISTS shift_closures;

-- Re-run migration
SOURCE db/migrations/007_create_qc_module_tables.sql;
SOURCE db/seeds/qc_module_seed_data.sql;
```

## 📝 Production Checklist

For production deployment:

- [ ] **Remove Seed Data**
  - [ ] Don't run qc_module_seed_data.sql
  - [ ] Create real checklist items for products

- [ ] **Security**
  - [ ] Change default passwords
  - [ ] Enable HTTPS
  - [ ] Set proper file permissions (644 files, 755 dirs)
  - [ ] Disable debug mode in config.php

- [ ] **Backup Strategy**
  - [ ] Set up daily backups
  - [ ] Test restore procedure

- [ ] **Monitoring**
  - [ ] Set up error logging
  - [ ] Monitor upload directory size
  - [ ] Track decision counts

## ✅ Sign-Off

Deployment completed by: ________________  
Date: ________________  
Environment: [ ] Development [ ] Staging [ ] Production

**Verification:**
- [ ] All migrations run successfully
- [ ] All tests passed
- [ ] Documentation reviewed
- [ ] User trained
- [ ] Backup created

---

**Status:** Ready for production use! 🎉
