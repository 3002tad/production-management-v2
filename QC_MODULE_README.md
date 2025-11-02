# QC Module - Quality Control & Verification

## 📋 Overview

The QC (Quality Control) module enables QC staff to inspect shift closures, verify product quality against checklists, and make APPROVE/REJECT decisions. When rejected, the system automatically creates adjustment requests for production leaders.

## 🎯 Features

- ✅ View pending shift closures awaiting QC inspection
- ✅ Create QC inspection sessions
- ✅ Input inspection results against product-specific checklists
- ✅ Upload photo/video evidence
- ✅ AI-powered decision recommendations based on AQL (Acceptance Quality Limit)
- ✅ APPROVE/REJECT decisions with full audit trail
- ✅ Automatic adjustment request generation for rejections
- ✅ Near-threshold detection (requests sample size increase)
- ✅ Role-based access control (QC staff only)

## 🗂️ Database Schema

### Tables Created (Migration 007)

1. **shift_closures** - End-of-shift production records
2. **qc_sessions** - QC inspection sessions
3. **qc_items** - Individual checklist item results
4. **qc_decisions** - Final APPROVE/REJECT decisions
5. **qc_attachments** - Evidence photos/videos
6. **adjustment_requests** - Generated when QC rejects
7. **qc_checklist_master** - Product checklist definitions
8. **qc_config** - Configuration parameters

## 🚀 Installation & Setup

### Step 1: Run Migration

```sql
-- In phpMyAdmin or MySQL client
SOURCE db/migrations/007_create_qc_module_tables.sql;
```

### Step 2: Insert Seed Data

```sql
-- Load sample data for testing
SOURCE db/seeds/qc_module_seed_data.sql;
```

### Step 3: Create Upload Directory

```bash
# Windows PowerShell
mkdir uploads/qc
```

### Step 4: Update Routes (if needed)

Routes are automatically available:
- `GET /qc/` - Pending closures list
- `GET /qc/pending` - Same as above
- `POST /qc/createSession` - Create new QC session
- `GET /qc/sessions/{id}` - View session details
- `POST /qc/sessions/{id}/items` - Save checklist items
- `POST /qc/sessions/{id}/attachments` - Upload files
- `POST /qc/sessions/{id}/decision` - Make APPROVE/REJECT decision
- `GET /qc/adjustments` - View adjustment requests

## 👤 Test User

**Username:** `qc_inspector`  
**Password:** `password`  
**Role:** QC Staff

## 📖 Usage Workflow

### Happy Path: APPROVE Decision

1. **Login** as QC user
2. **Navigate** to `/qc/` - See pending closures
3. **Click "Inspect"** on a closure - Creates new QC session
4. **Fill Checklist**:
   - Select PASS/FAIL for each item
   - Enter defect count if FAIL
   - Choose severity (MINOR/MAJOR/CRITICAL)
   - Add notes
5. **Click "Save Checklist"**
6. **View Recommendation** - AI suggests decision based on AQL
7. **Upload Evidence** (optional photos/videos)
8. **Click "APPROVE"** - System:
   - Updates session status to DECIDED
   - Updates closure status to VERIFIED
   - Sets `can_receive_fg = 1` (warehouse can receive)
   - Locks session (no more edits)

### Alternative Path: REJECT Decision

1-5. Same as above
6. **Click "REJECT"**
7. **Enter Reason** in modal (required)
8. **Ensure ≥1 Attachment** exists (validation)
9. **Confirm Reject** - System:
   - Updates session status to DECIDED
   - Updates closure status to REJECTED
   - Creates adjustment request with status OPEN
   - Assigns to line manager (configurable)

### Alternative Path: Near-Threshold Warning

When defect rate is within 5% margin of AQL threshold:

```
Example:
- AQL = 2.5%
- Defect Rate = 5.0% (within 2.5% ± 5%)
```

System returns **HTTP 409** with:
```json
{
  "code": "NEAR_THRESHOLD",
  "action": "INCREASE_SAMPLE",
  "message": "Defect rate 5.0% is near threshold. Recommend increasing sample size."
}
```

QC inspector should:
1. Increase sample size
2. Re-inspect additional items
3. Save new results
4. Make decision again

## 🔧 API Endpoints

### POST /qc/createSession
**Body:** `closure_id=123`  
**Response:** Redirects to `/qc/sessions/{id}`

### POST /qc/sessions/{id}/items
**Body:**
```json
{
  "items": [
    {
      "checklist_item_code": "CHK-BP-001-01",
      "checklist_item_name": "Visual Inspection",
      "result": "PASS",
      "defect_count": 0,
      "severity": null,
      "note": "All clean"
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "QC items saved successfully",
  "count": 5
}
```

### POST /qc/sessions/{id}/attachments
**Body:** `multipart/form-data` with `file` field  
**Allowed:** JPG, PNG, GIF, MP4, MOV (max 10MB)

**Response:**
```json
{
  "success": true,
  "attachment": {
    "id": 123,
    "filename": "defect_photo.jpg",
    "url": "http://localhost/uploads/qc/2025/11/xyz.jpg"
  }
}
```

### POST /qc/sessions/{id}/decision
**Body:**
```json
{
  "result": "APPROVE",  // or "REJECT"
  "aql": 2.5,
  "reason": "Critical defects found..."  // Required for REJECT
}
```

**Success Response:**
```json
{
  "success": true,
  "message": "Decision APPROVE recorded successfully",
  "defect_rate": 2.1
}
```

**Near-Threshold Response (409):**
```json
{
  "code": "NEAR_THRESHOLD",
  "action": "INCREASE_SAMPLE",
  "message": "Defect rate 5.0% is near threshold",
  "defect_rate": 5.0,
  "aql_threshold": 2.5
}
```

**Validation Error (400):**
```json
{
  "error": "Validation failed",
  "errors": [
    "Reason is required for REJECT decision.",
    "At least one attachment is required for REJECT."
  ]
}
```

## 🧪 Testing

### Manual Test Cases

#### Test 1: Happy Path APPROVE
```
1. Login as qc_inspector
2. Go to /qc/
3. Click "Inspect" on SC-20251102-LINE01-CA1
4. Fill checklist with all PASS results
5. Save checklist
6. Click APPROVE
7. Verify:
   ✓ Session status = DECIDED
   ✓ Closure status = VERIFIED
   ✓ can_receive_fg = 1
   ✓ Decision record created
```

#### Test 2: REJECT Requires Reason + Attachment
```
1. Create new session
2. Fill checklist with some FAIL results
3. Click REJECT without entering reason
4. Expect: Validation error "Reason required"
5. Enter reason but no attachment
6. Expect: Validation error "At least one attachment required"
7. Upload photo + enter reason
8. Confirm REJECT
9. Verify:
   ✓ Adjustment request created
   ✓ Closure status = REJECTED
```

#### Test 3: Near-Threshold Detection
```
1. Create session with defect rate ~5%
2. Try to decide
3. Expect: HTTP 409 with "INCREASE_SAMPLE" action
4. Add more checklist items
5. Re-save
6. Try decision again
```

#### Test 4: Session Lock After Decision
```
1. Complete a session with APPROVE
2. Try to save items again
3. Expect: HTTP 409 "Session is already decided"
4. Try to upload attachment
5. Expect: HTTP 409
```

#### Test 5: Permission Check
```
1. Login as non-QC user (e.g., worker)
2. Try to access /qc/
3. Expect: Redirect to login with error
4. Login as system_admin (level 90+)
5. Can access (higher level override)
```

### Automated Test Scenarios (Reference)

```php
// Test: APPROVE decision updates closure
public function test_approve_decision_updates_closure_status()
{
    // Arrange
    $session_id = $this->createTestSession();
    $this->addTestItems($session_id, 'all_pass');
    
    // Act
    $result = $this->qcModel->processApproveDecision($session_id, [
        'aql' => 2.5,
        'defect_rate' => 1.0,
        'decided_by' => 'qc_test'
    ]);
    
    // Assert
    $this->assertTrue($result);
    $session = $this->qcModel->getSessionById($session_id);
    $this->assertEquals('DECIDED', $session->status);
    $this->assertEquals('VERIFIED', $session->closure_status);
    $this->assertEquals(1, $session->can_receive_fg);
}

// Test: REJECT requires reason + attachment
public function test_reject_validation_requires_reason_and_attachment()
{
    // Arrange
    $session_id = $this->createTestSession();
    
    // Act
    $validation = $this->checklistService->validateDecision(
        $session_id, 
        'REJECT', 
        null  // No reason
    );
    
    // Assert
    $this->assertFalse($validation['valid']);
    $this->assertContains('Reason is required', $validation['errors']);
    $this->assertContains('at least one attachment', $validation['errors']);
}
```

## 📊 Sample Data Included

### Shift Closures
- `SC-20251102-LINE01-CA1` - PENDING_QC (5000 finished, 150 waste)
- `SC-20251101-LINE01-CA2` - VERIFIED (already approved)
- `SC-20251102-LINE02-CA1` - PENDING_QC (3500 finished, 200 waste)
- `SC-20251031-LINE01-CA3` - REJECTED (2000 finished, 500 waste)

### Checklist Items
- PROD-BP-001 (Blue/Black Ink Pen): 5 checklist items
- PROD-BP-002 (Red Ink Pen): 4 checklist items

### QC Sessions
- `QCS-20251102-0001` - OPEN (in progress)
- `QCS-20251101-0001` - DECIDED/APPROVED
- `QCS-20251031-0001` - DECIDED/REJECTED

### Adjustment Requests
- `AR-20251031-0001` - OPEN (from rejected session)

## ⚙️ Configuration

Located in `qc_config` table:

| Key | Value | Description |
|-----|-------|-------------|
| QC_AQL_DEFAULT | 2.5 | Default Acceptance Quality Limit (%) |
| QC_NEAR_THRESHOLD_MARGIN | 5 | Margin for near-threshold warning (%) |
| QC_MAX_UPLOAD_SIZE | 10485760 | Max upload size (10MB) |
| QC_ALLOWED_MIME_TYPES | image/jpeg,image/png,... | Allowed file types |

**To modify:**
```sql
UPDATE qc_config SET config_value = '3.0' WHERE config_key = 'QC_AQL_DEFAULT';
```

## 🔐 Permissions

### Role Requirements
- **Primary:** `qc_staff` (role_id = 5, level = 60)
- **Override:** Users with level ≥ 60 can access

### Line Assignment (Optional)
If users have `line_code` in session, system enforces:
- QC can only inspect closures from their assigned line
- Prevents cross-line access

**Implementation:**
```php
// In Login controller, add to session:
$session_data['line_code'] = 'LINE-01';  // From user profile

// QC controller checks:
if ($user_line && $session->line_code !== $user_line) {
    // Access denied
}
```

## 🐛 Troubleshooting

### Issue: "No checklist defined for this product"
**Solution:** Add checklist items to `qc_checklist_master`:
```sql
INSERT INTO qc_checklist_master (code, product_code, item_name, criteria, aql, category, sequence)
VALUES ('CHK-PROD-01', 'YOUR_PRODUCT_CODE', 'Test Item', 'Criteria...', 2.5, 'visual', 1);
```

### Issue: Upload fails with "Failed to save attachment"
**Solution:**
1. Check `uploads/qc/` directory exists
2. Verify write permissions (chmod 755)
3. Check file size < 10MB
4. Verify MIME type is allowed

### Issue: "Session not found"
**Solution:** Ensure:
1. Session was created via `POST /qc/createSession`
2. URL has correct session ID
3. Session wasn't deleted

### Issue: Decision returns 409 "Session already decided"
**Solution:** Session is locked after decision. Cannot modify. Create new session if re-inspection needed.

## 🔄 Integration Points

### Warehouse Module (Future)
When QC approves:
```php
// Warehouse can check:
$closure = $db->get_where('shift_closures', [
    'id' => $id,
    'can_receive_fg' => 1
])->row();

if ($closure) {
    // Allow receiving finished goods
}
```

### Event Bus (Stub)
```php
// In QcModel::processApproveDecision()
// Emit event for other modules
EventBus::emit('QC_APPROVED', [
    'closure_id' => $closure_id,
    'session_id' => $session_id,
    'defect_rate' => $defect_rate
]);
```

## 📝 Code Files

```
application/
├── controllers/
│   └── Qc.php                    # Main QC controller
├── models/
│   └── QcModel.php               # Data access layer
├── libraries/
│   └── ChecklistService.php     # Business logic
└── views/
    └── qc/
        ├── pending.php           # Pending closures list
        ├── session.php           # Inspection session form
        └── adjustments.php       # Adjustment requests

db/
├── migrations/
│   └── 007_create_qc_module_tables.sql
└── seeds/
    └── qc_module_seed_data.sql

uploads/
└── qc/                           # Attachment storage
    └── YYYY/MM/                  # Organized by date
```

## 🎓 Best Practices

1. **Always upload evidence for REJECT** - Provides audit trail
2. **Review AI recommendations** - But use human judgment
3. **Increase sample size if near threshold** - Better accuracy
4. **Add notes to checklist items** - Helps future analysis
5. **Complete all checklist items** - Don't skip
6. **Lock sessions after decision** - Maintains data integrity

## 🚧 Future Enhancements

- [ ] Statistical Process Control (SPC) charts
- [ ] Trend analysis across sessions
- [ ] QC performance metrics dashboard
- [ ] Mobile app for on-floor inspection
- [ ] Barcode scanning for lot tracking
- [ ] Email notifications for rejections
- [ ] Integration with ERP systems

## 📞 Support

For issues or questions:
- Check logs: `application/logs/`
- Review error responses from API
- Verify database constraints
- Contact: dev@production.com

---

**Version:** 1.0.0  
**Last Updated:** November 2, 2025  
**Author:** AI Pair Programmer
