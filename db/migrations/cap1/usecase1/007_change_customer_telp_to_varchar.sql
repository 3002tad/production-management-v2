-- Migration: Change `customer.telp` from INT to VARCHAR(20)
-- Reason: phone numbers can have leading zeros and exceed INT range; store as string of digits

ALTER TABLE `customer` 
  MODIFY COLUMN `telp` VARCHAR(20) NOT NULL COMMENT 'Số điện thoại (chuỗi chữ số)';

-- Optional: update any existing overflowed values if known; careful review required before running on production.
-- Example safe operation to cast existing numeric values to string (keeps current stored numeric):
-- UPDATE `customer` SET `telp` = LPAD(CAST(`telp` AS CHAR), 0, '0');
-- Note: Above is placeholder; do NOT run without manual review and backup.
