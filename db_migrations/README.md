This folder contains SQL migrations for manual execution.

Migration: 001_alter_finished_receipt_autoinc.sql
Purpose:
- Ensure `finished_receipt.id_receipt` is a PRIMARY KEY with AUTO_INCREMENT.
- Sets AUTO_INCREMENT to MAX(id_receipt)+1 to avoid conflicts.

How to run (Windows PowerShell):
1. Backup your database first. Example using mysqldump:

   mysqldump -u <user> -p <database> > backup_before_autoinc.sql

2. Run the migration:

   mysql -u <user> -p <database> < db_migrations\\001_alter_finished_receipt_autoinc.sql

3. Check output. The script will:
   - Show duplicate id_receipt rows (if any). If duplicates exist, stop and fix them first.
   - Add a PRIMARY KEY if missing.
   - Modify `id_receipt` column to AUTO_INCREMENT and set the next value to max+1.
   - Print the column definition and last 5 rows for verification.

Notes & safety:
- Always backup before running ALTERs.
- If the script reports duplicate id_receipt rows, do NOT proceed; fix duplicates manually (merge or renumber) and retry.
- If your deployment has triggers or foreign keys referencing this table, review them after the change.
