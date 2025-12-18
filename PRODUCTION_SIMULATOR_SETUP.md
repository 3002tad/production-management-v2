# Production Simulator - Quick Setup Checklist

**Date:** 2025-12-18

---

## ✅ Files Created (6 files)

### Database
- [x] `db/migrations/Cap2/CaLamViec/016_create_production_simulator_tables.sql`
  - Tables: production_records, simulator_settings, downtime_reasons
  - View: v_production_summary
  - Default settings inserted

### Backend
- [x] `application/models/leader/ProductionSimulatorModel.php`
  - Settings management
  - Production records CRUD
  - Shift machines retrieval
  
- [x] `application/controllers/leader/ProductionSimulator.php`
  - `/settings` - UI page
  - `/run` - Simulator endpoint (AJAX)
  - `/toggle` - ON/OFF (AJAX)
  - `/update_settings` - Update config (AJAX)
  - `/status` - Check status (AJAX)
  - `/get_shift_records/{id}` - Get data (AJAX)

### Frontend
- [x] `application/views/leader/simulator/settings.php`
  - Toggle ON/OFF
  - Configure interval, ranges, probabilities
  - Real-time status indicator
  
- [x] `application/views/leader/shift/detail.php` (UPDATED)
  - New tab "Sản lượng"
  - Auto-polling JavaScript
  - Production summary cards
  - Records table

### Documentation
- [x] `PRODUCTION_SIMULATOR_GUIDE.md`
  - Complete user guide
  - Architecture overview
  - API documentation
  - Troubleshooting

---

## 🚀 Setup Steps

### 1. Run Migration
```bash
# Open phpMyAdmin or MySQL client
# Execute:
db/migrations/Cap2/CaLamViec/016_create_production_simulator_tables.sql
```

**Verify:**
```sql
SHOW TABLES LIKE '%production%';
-- Expected: production_records

SHOW TABLES LIKE '%simulator%';
-- Expected: simulator_settings

SELECT * FROM simulator_settings;
-- Should show 11 default settings
```

### 2. Access Settings Page
```
URL: http://localhost/leader/productionsimulator/settings

Default status: TẮT (disabled)
```

### 3. Configure & Enable
1. Review default settings (optional)
2. Click toggle ON/OFF to enable
3. Status indicator turns GREEN
4. Simulator starts auto-polling

### 4. Test with Shift
1. Create or open a shift with `status='running'`
2. Ensure shift has machines assigned (via shift_machine_staff)
3. Open shift detail page
4. Click "Sản lượng" tab
5. Data should appear within [interval] seconds

---

## 🔧 Default Configuration

| Setting | Value | Description |
|---------|-------|-------------|
| **Enabled** | `0` (OFF) | Must toggle ON manually |
| **Interval** | `300s` (5 min) | Auto-run frequency |
| **Good Count** | `50-200` | Products per record |
| **Defect Rate** | `1%-8%` | Defect percentage |
| **Downtime Prob** | `0.15` (15%) | Chance of downtime |
| **Downtime Duration** | `5-30 min` | When downtime occurs |
| **Target Multiplier** | `1.2` | Target = good × 1.2 |
| **Active Shifts Only** | `1` (Yes) | Only 'running' shifts |

---

## 🧪 Testing Checklist

### ✅ Settings Page
- [ ] Page loads at `/leader/productionsimulator/settings`
- [ ] Status badge shows "TẮT" initially
- [ ] Toggle switch works (ON/OFF)
- [ ] Status indicator changes color (gray → green)
- [ ] Form fields show default values
- [ ] "Lưu cài đặt" button saves successfully
- [ ] Success notification appears after save

### ✅ Shift Detail Page
- [ ] New tab "Sản lượng" appears
- [ ] Badge shows count (0 initially)
- [ ] Simulator status text displays correctly
- [ ] "Cài đặt" link opens settings in new tab
- [ ] "Làm mới" button works
- [ ] Summary cards display (after data created)
- [ ] Records table populates (after data created)

### ✅ Auto-Polling
- [ ] Console log shows "Simulator polling started"
- [ ] Console log shows "Running simulator at [time]"
- [ ] Runs every [interval] seconds
- [ ] Badge count updates automatically
- [ ] Summary cards refresh when tab active
- [ ] Polling stops when simulator disabled

### ✅ Data Generation
- [ ] Records created in `production_records` table
- [ ] good_count within configured range
- [ ] defect_count calculated correctly
- [ ] downtime occurs based on probability
- [ ] efficiency_rate calculated (produced/target × 100)
- [ ] defect_rate calculated (defect/good × 100)
- [ ] is_simulated = 1 for all records

---

## 🐛 Quick Troubleshooting

### Issue: Simulator không tạo dữ liệu
```sql
-- Check enabled
SELECT setting_value FROM simulator_settings WHERE setting_key = 'simulator_enabled';
-- Should return '1'

-- Check active shifts
SELECT * FROM production_shifts WHERE shift_status = 'running';
-- Should have at least 1 shift

-- Check machines assigned
SELECT * FROM shift_machine_staff WHERE shift_id = YOUR_SHIFT_ID;
-- Should have machines assigned
```

### Issue: Tab không hiển thị
- Clear browser cache (Ctrl+Shift+Delete)
- Hard refresh (Ctrl+F5)
- Check browser console for errors (F12)

### Issue: Auto-polling không chạy
- Simulator phải BẬT (check status)
- Tab phải visible (polling pauses when hidden)
- Check console: `runSimulator()` to test manually

---

## 📊 Sample SQL Queries

### View simulated data
```sql
SELECT 
    pr.timestamp,
    m.code AS machine_code,
    pr.good_count,
    pr.defect_count,
    pr.efficiency_rate,
    pr.downtime_minutes
FROM production_records pr
JOIN machines m ON pr.machine_id = m.id
WHERE pr.is_simulated = 1
ORDER BY pr.timestamp DESC
LIMIT 20;
```

### Production summary
```sql
SELECT * FROM v_production_summary 
WHERE shift_id = YOUR_SHIFT_ID;
```

### Delete old simulated data
```sql
DELETE FROM production_records 
WHERE is_simulated = 1 
  AND timestamp < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

---

## 🎯 Next Steps

After successful setup:

1. **Customize settings** for your use case:
   - Faster interval (60s) for testing
   - Adjust ranges to match real production data
   
2. **Create test shifts** with:
   - Multiple machines
   - Assigned staff
   - Status = 'running'

3. **Monitor data generation**:
   - Check production tab regularly
   - Verify calculations are correct
   - Adjust settings as needed

4. **Integrate with reporting**:
   - Use production_records for analytics
   - Create charts/graphs
   - Export to Excel/PDF

---

## 📚 Documentation Reference

- **Full Guide:** `PRODUCTION_SIMULATOR_GUIDE.md`
- **Migration:** `016_create_production_simulator_tables.sql`
- **Model:** `ProductionSimulatorModel.php`
- **Controller:** `ProductionSimulator.php`

---

**Status:** ✅ Ready for testing  
**Estimated Setup Time:** 5-10 minutes  
**Dependencies:** Migration 015 (machine_role field)
