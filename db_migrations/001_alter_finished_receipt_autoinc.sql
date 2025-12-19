-- Migration: Make finished_receipt.id_receipt PRIMARY KEY and AUTO_INCREMENT
-- Safety-first: this script first checks that the table exists in the current database
-- and prints a helpful message if it does not, avoiding "Unknown table" errors.
-- Usage: mysql -u <user> -p <database> < 001_alter_finished_receipt_autoinc.sql

DELIMITER $$
CREATE PROCEDURE migrate_finished_receipt_autoinc()
BEGIN
  -- If table doesn't exist, print a message and skip all changes
  IF (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'finished_receipt') = 0 THEN
    SELECT 'TABLE finished_receipt NOT FOUND IN CURRENT DATABASE. Please select the correct database or restore the table before running this migration.' AS message;
  ELSE
    -- 1) Show any duplicate id_receipt (if any, fix before running)
    SELECT id_receipt, COUNT(*) AS cnt
    FROM finished_receipt
    GROUP BY id_receipt
    HAVING cnt > 1;

    -- If the resultset above is non-empty, stop and resolve duplicates before proceeding.

    -- 2) Ensure the column is non-null (no-op if already NOT NULL)
    ALTER TABLE finished_receipt MODIFY id_receipt INT NOT NULL;

    -- 3) Add PRIMARY KEY if it does not already exist
    IF (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS tc
        WHERE tc.TABLE_SCHEMA = DATABASE()
          AND tc.TABLE_NAME = 'finished_receipt'
          AND tc.CONSTRAINT_TYPE = 'PRIMARY KEY') = 0 THEN
      ALTER TABLE finished_receipt ADD PRIMARY KEY (id_receipt);
    END IF;

    -- 4) Compute next AUTO_INCREMENT value and set the column to AUTO_INCREMENT
    SET @max_id = (SELECT IFNULL(MAX(id_receipt), 0) FROM finished_receipt);
    SET @next = @max_id + 1;
    SET @alter = CONCAT('ALTER TABLE finished_receipt MODIFY id_receipt INT NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=', @next);
    PREPARE s2 FROM @alter;
    EXECUTE s2;
    DEALLOCATE PREPARE s2;

    -- 5) Verify result
    SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, EXTRA
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'finished_receipt'
      AND COLUMN_NAME = 'id_receipt';

    SELECT * FROM finished_receipt ORDER BY id_receipt DESC LIMIT 5;
  END IF;
END$$
DELIMITER ;

CALL migrate_finished_receipt_autoinc();
DROP PROCEDURE IF EXISTS migrate_finished_receipt_autoinc;
