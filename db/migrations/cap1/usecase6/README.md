Run the UC6 DB migration to add unique constraints

File: 002_add_user_unique_constraints.sql

Purpose:
- Adds a UNIQUE index on `user.username` (`ux_user_username`) to enforce uniqueness at DB level.
- Adds a UNIQUE index on `user.staff_id` (`ux_user_staff_id`) to prevent assigning multiple users to the same staff record (allows NULLs).

Important:
- Review data for duplicates before running. If duplicates exist, the ALTER will fail.
- Recommended steps:
  1. Backup your database.
  2. Check duplicates:
     - `SELECT username, COUNT(*) c FROM user GROUP BY username HAVING c > 1;`
     - `SELECT staff_id, COUNT(*) c FROM user WHERE staff_id IS NOT NULL GROUP BY staff_id HAVING c > 1;`
  3. Resolve duplicates (delete or merge records) if any.
  4. Run the migration SQL with your MySQL client:

     mysql -u <user> -p db_production < db/migrations/cap1/usecase6/002_add_user_unique_constraints.sql

- After applying, test UC6 flows (create user, edit role, lock/unlock, reset password) to ensure behavior remains correct.

If you want, I can prepare a small rollback script or apply the migration for you if you provide DB access or run it in a test environment.