@echo off
REM ========================================
REM APPLY STOCK ALLOCATION FIX
REM ========================================
echo.
echo ========================================
echo   STOCK ALLOCATION FIX - AUTO APPLY
echo ========================================
echo.

REM Thiet lap bien
set DB_NAME=db_production
set DB_USER=root
set BACKUP_FILE=backup_before_stock_fix_%date:~-4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%%time:~6,2%.sql
set BACKUP_FILE=%BACKUP_FILE: =0%

echo [1/5] Tao backup database...
mysqldump -u %DB_USER% -p %DB_NAME% > ..\backups\%BACKUP_FILE%
if errorlevel 1 (
    echo [ERROR] Backup failed!
    pause
    exit /b 1
)
echo [OK] Backup thanh cong: %BACKUP_FILE%
echo.

echo [2/5] Chay migration 008...
mysql -u %DB_USER% -p %DB_NAME% < 008_add_stock_allocation_trigger.sql
if errorlevel 1 (
    echo [ERROR] Migration failed!
    echo Rollback bang cach restore backup:
    echo mysql -u %DB_USER% -p %DB_NAME% ^< ..\backups\%BACKUP_FILE%
    pause
    exit /b 1
)
echo [OK] Migration thanh cong
echo.

echo [3/5] Chay test script...
mysql -u %DB_USER% -p %DB_NAME% < 008_test_stock_allocation.sql > test_result.txt
echo [OK] Ket qua test da duoc luu vao: test_result.txt
echo.

echo [4/5] Kiem tra consistency...
mysql -u %DB_USER% -p -e "USE %DB_NAME%; CALL sp_check_stock_consistency();" > consistency_check.txt
echo [OK] Ket qua consistency: consistency_check.txt
echo.

echo [5/5] Hoan tat!
echo.
echo ========================================
echo   CAC FILE DA TAO
echo ========================================
echo 1. Backup: ..\backups\%BACKUP_FILE%
echo 2. Test result: test_result.txt
echo 3. Consistency check: consistency_check.txt
echo.
echo ========================================
echo   BUOC TIEP THEO
echo ========================================
echo 1. Mo test_result.txt de xem ket qua test
echo 2. Neu tat ca PASS → Test tren giao dien
echo 3. Neu FAIL → Lien he dev de debug
echo.
echo 4. Xem stock real-time:
echo    mysql -u root -p -e "USE %DB_NAME%; SELECT * FROM v_product_stock_status;"
echo.
echo 5. Xem lich su phan bo:
echo    mysql -u root -p -e "USE %DB_NAME%; SELECT * FROM stock_allocation ORDER BY allocated_at DESC LIMIT 10;"
echo.
pause
