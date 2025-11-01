# Database Backups

Thư mục này dùng để lưu trữ các bản backup database.

## 📋 Naming Convention

```
db_production_backup_YYYYMMDD.sql           # Daily backup
db_production_backup_YYYYMMDD_HHMM.sql      # Backup với timestamp cụ thể
db_production_before_migration_XXX.sql      # Backup trước migration
db_production_structure_only.sql            # Chỉ structure
db_production_data_only.sql                 # Chỉ data
```

## 🔧 Quick Commands

### Tạo Backup

```bash
# Full backup
mysqldump -u root -p db_production > backups/db_production_backup_$(date +%Y%m%d).sql

# Structure only
mysqldump -u root -p --no-data db_production > backups/db_production_structure.sql

# Specific tables
mysqldump -u root -p db_production user roles permissions > backups/rbac_tables_backup.sql
```

### Restore Backup

```bash
# Restore full database
mysql -u root -p db_production < backups/db_production_backup_YYYYMMDD.sql

# Restore specific tables
mysql -u root -p db_production < backups/rbac_tables_backup.sql
```

## ⚠️ Important Notes

- **KHÔNG commit** files backup lên Git (đã có trong .gitignore)
- Backup trước mỗi migration lớn
- Giữ backups ít nhất 30 ngày
- Nén backups cũ: `gzip db_production_backup_20231109.sql`

## 🗑️ Cleanup Old Backups

```bash
# Xóa backups cũ hơn 30 ngày (Linux/Mac)
find backups/ -name "*.sql" -mtime +30 -delete

# Windows PowerShell
Get-ChildItem backups/*.sql | Where-Object {$_.LastWriteTime -lt (Get-Date).AddDays(-30)} | Remove-Item
```
