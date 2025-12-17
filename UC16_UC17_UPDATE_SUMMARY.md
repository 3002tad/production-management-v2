# Update Summary: UC16 & UC17 Hierarchy Integration

## Overview
Đã cập nhật module **UC16 (Điều phối - Leader)** và **UC17 (Xử lý kỹ thuật - Technical)** để sử dụng hierarchy **Zone → Line → Machine → Shift** giống UC15.

---

## 📊 UC16_GN_DP (Leader Coordination) - COMPLETED ✅

### Controller Changes (`application/controllers/UC16_GN_DP/UC16_GN_DP.php`)

**Before (lines 118-124):**
```php
// OLD: Query old machine table
$machines = $this->db->select('id_machine, machine_name')
    ->get('machine')
    ->result();
```

**After (lines 118-138):**
```php
// NEW: Full hierarchy with JOINs
$this->db->select('m.id, m.code as machine_code, m.name as machine_name, m.stage_type, pl.line_code, pl.line_name, z.zone_name');
$this->db->from('machines m');
$this->db->join('production_lines pl', 'm.line_id = pl.id', 'left');
$this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
$this->db->where('m.status', 'active');
$this->db->order_by('z.zone_code, pl.line_code, m.code');
$machines = $this->db->get()->result();

// Load production lines for line-wide assignments
$this->db->select('pl.id, pl.line_code, pl.line_name, z.zone_name');
$this->db->from('production_lines pl');
$this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
$this->db->order_by('z.zone_code, pl.line_code');
$lines = $this->db->get()->result();

// Add to data array
'machines' => $machines,
'lines' => $lines,
```

**Impact:**
- ✅ Machine dropdown shows full context: `Zone A - LINE01 > M001 - Machine Name (Stage)`
- ✅ Grouped by zone/line for easier navigation
- ✅ Only active machines displayed

---

### View Changes

#### 1. `application/views/uc16_gn_dp/coordination.php` (lines 28-47)

**Before:**
```php
<select name="machine_id" class="form-control">
  <option value="">-- Chọn máy --</option>
  <?php foreach ($machines as $m): ?>
    <option value="<?= $m->id_machine; ?>"><?= $m->machine_name . ' (' . $m->id_machine . ')'; ?></option>
  <?php endforeach; ?>
</select>
```

**After:**
```php
<select name="machine_id" class="form-control">
  <option value="">-- Chọn máy --</option>
  <?php if (!empty($machines)): 
    $current_zone = '';
    foreach ($machines as $m): 
      $zone_line_label = ($m->zone_name ?? 'N/A') . ' - ' . ($m->line_code ?? 'N/A');
      if ($current_zone != $zone_line_label):
        if ($current_zone != '') echo '</optgroup>';
        $current_zone = $zone_line_label;
        echo '<optgroup label="' . htmlspecialchars($current_zone) . '">';
      endif;
  ?>
    <option value="<?= $m->id; ?>">
      <?= htmlspecialchars($m->machine_code); ?> - <?= htmlspecialchars($m->machine_name); ?> (<?= htmlspecialchars($m->stage_type); ?>)
    </option>
  <?php endforeach; 
    if ($current_zone != '') echo '</optgroup>';
  endif; ?>
</select>
```

**UI Output:**
```
-- Chọn máy --
┣━ Zone A - LINE01
┃  ├─ M001 - Machine Name (Stage Type)
┃  └─ M002 - Machine Name (Stage Type)
┗━ Zone B - LINE02
   └─ M003 - Machine Name (Stage Type)
```

---

#### 2. `application/views/uc16_gn_dp/index.php` (line 31)

**Before:**
```php
<td><?= $incident->machine_name ?? $incident->id_machine; ?></td>
```

**After:**
```php
<td>
  <?php if (!empty($incident->zone_name)): ?>
    <small class="text-muted"><?= htmlspecialchars($incident->zone_name); ?></small> &rarr; 
  <?php endif; ?>
  <?php if (!empty($incident->line_code)): ?>
    <span class="badge badge-secondary"><?= htmlspecialchars($incident->line_code); ?></span>
  <?php endif; ?>
  <?php if (!empty($incident->machine_code)): ?>
    &rarr; <strong><?= htmlspecialchars($incident->machine_code); ?></strong>
  <?php else: ?>
    <em class="text-muted">(Toàn line)</em>
  <?php endif; ?>
</td>
```

**UI Output:**
- With machine: `Zone A → LINE01 → M001`
- Line-wide: `Zone A → LINE01 (Toàn line)`

---

#### 3. `application/views/uc16_gn_dp/view.php` (lines 12-21)

**Before:**
```php
<div class="row mb-4">
  <div class="col-md-6">
    <h6>Máy</h6>
    <p><?= $incident->machine_name ?? $incident->id_machine; ?></p>
  </div>
  <div class="col-md-6">
    <h6>Trạng thái</h6>
    <p>...</p>
  </div>
</div>
```

**After:**
```php
<!-- Location & Hierarchy Card -->
<div class="card bg-light mb-4">
  <div class="card-body">
    <h6 class="mb-3"><i class="fas fa-map-marker-alt"></i> Vị trí & Thông tin</h6>
    <div class="row">
      <div class="col-md-3">
        <small class="text-muted">Ca làm việc</small>
        <p class="mb-0"><span class="badge badge-info"><?= $incident->shift_code; ?></span></p>
        <small><?= $incident->shift_name; ?></small>
      </div>
      <div class="col-md-3">
        <small class="text-muted">Khu vực</small>
        <p class="mb-0"><?= $incident->zone_name ?? 'N/A'; ?></p>
      </div>
      <div class="col-md-3">
        <small class="text-muted">Dây chuyền</small>
        <p class="mb-0"><span class="badge badge-secondary"><?= $incident->line_code ?? 'N/A'; ?></span></p>
        <small><?= $incident->line_name; ?></small>
      </div>
      <div class="col-md-3">
        <small class="text-muted">Máy móc</small>
        <p class="mb-0"><span class="badge badge-dark"><?= $incident->machine_code; ?></span></p>
        <small><?= $incident->machine_name; ?></small>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-6">
    <h6>Trạng thái</h6>
    <p>...</p>
  </div>
</div>
```

**UI Output:**
```
┌────────────────────────────────────────────────────────────────┐
│ 📍 Vị trí & Thông tin                                          │
├────────────┬──────────────┬──────────────┬─────────────────────┤
│ Ca làm việc │ Khu vực      │ Dây chuyền   │ Máy móc             │
│ [CA1]      │ Zone A       │ [LINE01]     │ [M001]              │
│ 07:00-15:00│              │ Line Name    │ Machine Name        │
└────────────┴──────────────┴──────────────┴─────────────────────┘
```

---

## 🔧 UC17_XLSC (Technical Repair) - COMPLETED ✅

### Controller Changes
**No changes needed** - UC17 đã sử dụng `UC15_BCSCModel` (có full hierarchy rồi)

---

### View Changes

#### 1. `application/views/uc17_xlsc/index.php` (line 29)

**Before:**
```php
<td class="pl-4"><?= $inc->id_machine; ?></td>
```

**After:**
```php
<td class="pl-4">
  <?php if (!empty($inc->zone_name)): ?>
    <small class="text-muted"><?= htmlspecialchars($inc->zone_name); ?></small> &rarr; 
  <?php endif; ?>
  <?php if (!empty($inc->line_code)): ?>
    <span class="badge badge-secondary"><?= htmlspecialchars($inc->line_code); ?></span>
  <?php endif; ?>
  <?php if (!empty($inc->machine_code)): ?>
    &rarr; <strong><?= htmlspecialchars($inc->machine_code); ?></strong>
  <?php else: ?>
    <em class="text-muted">(Toàn line)</em>
  <?php endif; ?>
</td>
```

**UI Output:** Same as UC16 index (Zone → Line → Machine)

---

#### 2. `application/views/uc17_xlsc/view.php` (lines 12-20)

**Before:**
```php
<div class="row mb-4">
  <div class="col-md-6">
    <h6>Máy</h6>
    <p><?= $incident->machine_name ?? $incident->id_machine; ?></p>
  </div>
  <div class="col-md-6">
    <h6>Trạng thái</h6>
    <p>...</p>
  </div>
</div>
```

**After:**
```php
<!-- Location & Hierarchy Card (same as UC16) -->
<div class="card bg-light mb-4">
  <div class="card-body">
    <h6 class="mb-3"><i class="fas fa-map-marker-alt"></i> Vị trí & Thông tin</h6>
    <div class="row">
      <div class="col-md-3">
        <small class="text-muted">Ca làm việc</small>
        <p class="mb-0"><span class="badge badge-info"><?= $incident->shift_code; ?></span></p>
        <small><?= $incident->shift_name; ?></small>
      </div>
      <div class="col-md-3">
        <small class="text-muted">Khu vực</small>
        <p class="mb-0"><?= $incident->zone_name ?? 'N/A'; ?></p>
      </div>
      <div class="col-md-3">
        <small class="text-muted">Dây chuyền</small>
        <p class="mb-0"><span class="badge badge-secondary"><?= $incident->line_code ?? 'N/A'; ?></span></p>
        <small><?= $incident->line_name; ?></small>
      </div>
      <div class="col-md-3">
        <small class="text-muted">Máy móc</small>
        <p class="mb-0"><span class="badge badge-dark"><?= $incident->machine_code; ?></span></p>
        <small><?= $incident->machine_name; ?></small>
      </div>
    </div>
  </div>
</div>
```

---

## 📝 Summary of Changes

### Field Name Migrations
| Old Field       | New Field(s)                                    | Source Table      |
|-----------------|-------------------------------------------------|-------------------|
| `id_machine`    | `id`                                            | `machines`        |
| `machine_name`  | `machine_code`, `machine_name`, `stage_type`    | `machines`        |
| *(new)*         | `line_code`, `line_name`                        | `production_lines`|
| *(new)*         | `zone_code`, `zone_name`                        | `zones`           |
| *(new)*         | `shift_code`, `shift_name`, `shift_date`        | `production_shifts`|

### Files Modified
✅ **UC16_GN_DP** (4 files):
- `controllers/UC16_GN_DP/UC16_GN_DP.php` - Controller query updates
- `views/uc16_gn_dp/coordination.php` - Grouped machine dropdown
- `views/uc16_gn_dp/index.php` - Table hierarchy display
- `views/uc16_gn_dp/view.php` - Detail card layout

✅ **UC17_XLSC** (2 files):
- `views/uc17_xlsc/index.php` - Table hierarchy display
- `views/uc17_xlsc/view.php` - Detail card layout

### Consistency Across Modules
| Feature                  | UC15 (Worker) | UC16 (Leader) | UC17 (Technical) |
|--------------------------|---------------|---------------|------------------|
| Zone → Line → Machine    | ✅            | ✅            | ✅               |
| Shift tracking           | ✅            | ✅            | ✅               |
| Grouped dropdowns        | ✅            | ✅            | N/A              |
| Hierarchy detail card    | ✅            | ✅            | ✅               |
| Material Design 3 badges | ✅            | ✅            | ✅               |

---

## 🎯 Next Steps

### 1. Test UC16 (Leader Coordination)
```bash
# Login as Leader/Line Manager
# Navigate to: UC16_GN_DP > View incident > Coordination form
# Expected: Machine dropdown grouped by "Zone A - LINE01" with optgroups
# Expected: Detail view shows 4-column hierarchy card
```

### 2. Test UC17 (Technical Repair)
```bash
# Login as Technical staff
# Navigate to: UC17_XLSC > View incident
# Expected: Detail view shows full hierarchy (Shift/Zone/Line/Machine)
# Expected: Index table shows "Zone A → LINE01 → M001"
```

### 3. Integration Test
```bash
# Test workflow: Worker (UC15) → Leader (UC16) → Technical (UC17)
1. Worker creates incident with Line + Machine
2. Leader coordinates via UC16 (assigns technical, replaces machine)
3. Technical handles via UC17 (submits estimate, updates status)
# Expected: All modules show consistent hierarchy context
```

### 4. Execute Migration 012 (Optional but Recommended)
```sql
-- Run in phpMyAdmin to add shift_id column + FK
SOURCE db/migrations/012_add_shift_to_incidents.sql;
```

---

## ✨ Benefits

1. **Consistent Data Model** - All 3 modules use same hierarchy structure
2. **Better UX** - Users see full context (Zone/Line/Machine/Shift) everywhere
3. **Easier Maintenance** - Shared UC15_BCSCModel, no duplicate queries
4. **Scalability** - Easy to add new zones/lines/machines without code changes
5. **Data Integrity** - Foreign keys ensure valid references to machines/lines/shifts

---

## 📌 Notes

- **Browser Cache**: Clear cache if old views still appear (Ctrl+Shift+R)
- **Old `machine` table**: Still exists but deprecated, all queries now use `machines` table
- **Line-wide incidents**: `id_machine` can be NULL, showing "(Toàn line)" in UI
- **Shift auto-detection**: Only implemented in UC15 add/edit forms, not in UC16/UC17

---

**Updated by:** GitHub Copilot  
**Date:** 2025-01-XX  
**Status:** ✅ COMPLETED - Ready for testing
