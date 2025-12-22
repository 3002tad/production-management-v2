# 📋 DANH SÁCH CHI TIẾT CẤU HÌNH KHOẢI UP HOST
# Production Management System v2 - CodeIgniter 3

---

## **1️⃣ FILE: index.php (ROOT)**
### Vị trí: `/index.php` (tại root project)

| # | Cấu hình | Giá trị Hiện Tại | Giá trị Production | Ghi chú |
|---|---------|-----------------|-------------------|---------|
| 1.1 | ENVIRONMENT | `development` | `production` | **IMPORTANT** - Ảnh hưởng error reporting |
| 1.2 | Timezone | `Asia/Ho_Chi_Minh` | `Asia/Ho_Chi_Minh` | Đã đúng, giữ nguyên |

**Dòng cần sửa:**
- Dòng ~56: `define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');`
  - Thay `'development'` → `'production'`

---

## **2️⃣ FILE: application/config/config.php**
### Vị trí: `/application/config/config.php`

| # | Setting Key | Giá trị Hiện Tại | Giá trị Production | Dòng | Ghi chú |
|---|------------|-----------------|-------------------|------|---------|
| 2.1 | `$config['base_url']` | `http://localhost:8080/production-management-v2/` | `https://pm.simplething.id.vn/` | ~24 | **CRITICAL** - URL gốc của app |
| 2.2 | `$config['index_page']` | `` (rỗi) | `` (rỗi) | ~30 | Đã đúng - vì dùng .htaccess |
| 2.3 | `$config['uri_protocol']` | `REQUEST_URI` | `REQUEST_URI` | ~52 | Đã đúng |
| 2.4 | `$config['url_suffix']` | `` (rỗi) | `` (rỗi) | ~60 | Đã đúng |
| 2.5 | `$config['language']` | `vietnamese` | `vietnamese` | ~70 | Đã đúng |
| 2.6 | `$config['charset']` | `UTF-8` | `UTF-8` | ~80 | Đã đúng |
| 2.7 | `$config['enable_hooks']` | `false` | `false` | ~88 | Đã đúng |
| 2.8 | `$config['encryption_key']` | `production-management-v2-key` | **Đổi sang key mạnh** | ~220 | **SECURITY** - Dùng key ngẫu nhiên 32 ký tự |
| 2.9 | `$config['log_threshold']` | `2` (Debug) | `1` (Errors only) | ~186 | **PERFORMANCE** - Giảm logging |
| 2.10 | `$config['sess_driver']` | `files` | `files` hoặc `database` | ~270 | Đã đúng (files nếu không dùng Redis) |
| 2.11 | `$config['sess_cookie_name']` | `ci_session` | `ci_session` hoặc tên custom | ~271 | Có thể đổi nếu muốn |
| 2.12 | `$config['sess_expiration']` | `7200` (2 giờ) | `7200` | ~272 | Có thể điều chỉnh |
| 2.13 | `$config['sess_save_path']` | `APPPATH . 'cache/sessions/'` | Đúng | ~273 | **IMPORTANT** - Phải có quyền ghi |
| 2.14 | `$config['cookie_prefix']` | `` (rỗi) | `` (rỗi) | ~281 | Đã đúng |
| 2.15 | `$config['cookie_domain']` | `` (rỗi) | `` (rỗi) | ~282 | Có thể thêm `.pm.simplething.id.vn` |
| 2.16 | `$config['cookie_path']` | `/` | `/` | ~283 | Đã đúng |
| 2.17 | `$config['cookie_secure']` | `false` | `true` | ~284 | **SECURITY** - HTTPS only |
| 2.18 | `$config['cookie_httponly']` | `false` | `true` | ~285 | **SECURITY** - Chỉ HTTP, không JS |
| 2.19 | `$config['compress_output']` | `false` | `true` | ~323 | **PERFORMANCE** - Nén output |
| 2.20 | `$config['csrf_protection']` | `false` | `true` | ~314 | **SECURITY** - Bảo vệ CSRF |

**Mã sửa đôi:**
```php
// Dòng ~24
$config['base_url'] = 'https://pm.simplething.id.vn/';

// Dòng ~220 - Tạo key mạnh
$config['encryption_key'] = 'your-random-32-character-key-here';

// Dòng ~186
$config['log_threshold'] = 1;

// Dòng ~284-285
$config['cookie_secure'] = true;
$config['cookie_httponly'] = true;

// Dòng ~323
$config['compress_output'] = true;

// Dòng ~314
$config['csrf_protection'] = true;
```

---

## **3️⃣ FILE: application/config/database.php**
### Vị trí: `/application/config/database.php`

| # | Setting Key | Giá trị Hiện Tại | Giá trị Production | Ghi chú |
|---|------------|-----------------|-------------------|---------|
| 3.1 | `$db['default']['hostname']` | `localhost` | `localhost` | Đúng nếu DB cùng server |
| 3.2 | `$db['default']['username']` | `root` | **Đổi** (vd: `pm_user`) | **SECURITY** - Không dùng root |
| 3.3 | `$db['default']['password']` | `` (rỗi) | **Set password mạnh** | **CRITICAL** |
| 3.4 | `$db['default']['database']` | `db_production` | `db_production` | Đã đúng |
| 3.5 | `$db['default']['dbdriver']` | `mysqli` | `mysqli` | Đã đúng |
| 3.6 | `$db['default']['char_set']` | `utf8mb4` | `utf8mb4` | Đã đúng |
| 3.7 | `$db['default']['dbcollat']` | `utf8mb4_unicode_ci` | `utf8mb4_unicode_ci` | Đã đúng |
| 3.8 | `$db['default']['pconnect']` | `false` | `false` | Đã đúng |
| 3.9 | `$db['default']['db_debug']` | `(ENVIRONMENT !== 'production')` | Tự động FALSE | Đã đúng |
| 3.10 | `$db['default']['cache_on']` | `false` | `false` | Đã đúng |
| 3.11 | `$db['default']['save_queries']` | `true` | **`false`** | **PERFORMANCE** - Tắt save queries |
| 3.12 | `$db['default']['stricton']` | `false` | `true` (optional) | Có thể bật strict mode |

**Mã sửa đôi:**
```php
$db['default'] = [
    'dsn' => '',
    'hostname' => 'localhost',
    'username' => 'pm_user',                    // Thay 'root'
    'password' => 'your_strong_password_here',  // Đặt password mạnh
    'database' => 'db_production',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => false,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => false,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
    'swap_pre' => '',
    'encrypt' => false,
    'compress' => false,
    'stricton' => true,                         // Có thể bật
    'failover' => [],
    'save_queries' => false,                    // Thay false
];
```

---

## **4️⃣ FILE: .htaccess (ROOT)**
### Vị trí: `/.htaccess` (tạo tại root project nếu chưa có)

**Cần tạo nếu chưa có:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>

# Tắt directory listing
Options -Indexes

# Bảo vệ file config
<FilesMatch "\.php$">
    Deny from all
</FilesMatch>

# Ngoại lệ cho file chính
<FilesMatch "^index\.php$">
    Allow from all
</FilesMatch>
```

---

## **5️⃣ APACHE VIRTUALHOST CONFIG**
### Vị trí: `/etc/apache2/sites-available/production-management.conf`

**Cần tạo file này với nội dung:**
```apache
<VirtualHost *:80>
    ServerName pm.simplething.id.vn
    ServerAlias www.pm.simplething.id.vn
    DocumentRoot /var/www/production-management-v2
    
    ErrorLog ${APACHE_LOG_DIR}/pm-error.log
    CustomLog ${APACHE_LOG_DIR}/pm-access.log combined
    
    <Directory /var/www/production-management-v2>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ index.php/$1 [L]
        </IfModule>
    </Directory>
</VirtualHost>

# SSL sẽ được tự động thêm bởi Certbot
```

---

## **6️⃣ MYSQL DATABASE**
### Vị trí: Trên server MySQL

| # | Item | Giá trị | Ghi chú |
|---|------|--------|---------|
| 6.1 | Database Name | `db_production` | Tạo với charset utf8mb4 |
| 6.2 | Database User | `pm_user` | **Tạo user mới, không dùng root** |
| 6.3 | Database Password | **Mật khẩu mạnh 16+ ký tự** | Lưu lại an toàn |
| 6.4 | Database Charset | `utf8mb4` | Bắt buộc |
| 6.5 | Database Collation | `utf8mb4_unicode_ci` | Bắt buộc |
| 6.6 | Import SQL File | `db/db_production_stable.sql` | Import sau khi tạo DB |

**SQL Commands cần chạy:**
```sql
-- 1. Tạo database
CREATE DATABASE db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Tạo user và grant quyền
CREATE USER 'pm_user'@'localhost' IDENTIFIED BY 'your_strong_password_here';
GRANT ALL PRIVILEGES ON db_production.* TO 'pm_user'@'localhost';
FLUSH PRIVILEGES;

-- 3. Import database (từ command line)
-- mysql -u pm_user -p db_production < /var/www/production-management-v2/db/db_production_stable.sql
```

---

## **7️⃣ PHP CONFIGURATION**
### Vị trí: `/etc/php/8.x/apache2/php.ini` (hoặc phiên bản PHP của bạn)

| # | Setting | Giá trị Khuyến nghị | Ghi chú |
|---|---------|-------------------|---------|
| 7.1 | `extension=mysqli` | **enable** | **CRITICAL** |
| 7.2 | `extension=mbstring` | **enable** | **CRITICAL** |
| 7.3 | `extension=intl` | **enable** | Cần cho i18n |
| 7.4 | `extension=json` | **enable** | **CRITICAL** |
| 7.5 | `extension=zip` | **enable** | Nếu upload file zip |
| 7.6 | `memory_limit` | `256M` | Đủ cho app này |
| 7.7 | `max_execution_time` | `300` (5 phút) | Cho background jobs |
| 7.8 | `max_input_time` | `300` | Cho upload file |
| 7.9 | `upload_max_filesize` | `64M` | Tùy theo tập tin |
| 7.10 | `post_max_size` | `64M` | Phải >= upload_max_filesize |
| 7.11 | `display_errors` | `Off` | **SECURITY** |
| 7.12 | `log_errors` | `On` | Log các lỗi |
| 7.13 | `error_log` | `/var/log/php/error.log` | Path để log lỗi |
| 7.14 | `date.timezone` | `Asia/Ho_Chi_Minh` | Đồng bộ với app |

---

## **8️⃣ FILE PERMISSIONS (QUYỀN THỨ MỤC)**
### Các quyền cần thiết trên server

```bash
# Folder cần 755, file cần 644
/var/www/production-management-v2/
├── application/
│   ├── cache/              → 777 (ghi logs)
│   ├── logs/               → 777 (ghi logs)
│   ├── config/             → 755 (đọc)
│   └── ...
├── uploads/                → 777 (ghi file)
├── asset/                  → 777 (nếu ghi file)
├── system/                 → 755 (chỉ đọc)
├── .htaccess               → 644
└── index.php               → 644
```

**Commands cần chạy:**
```bash
cd /var/www/production-management-v2

# Đặt owner
sudo chown -R www-data:www-data .

# Đặt quyền folder
sudo chmod -R 755 .

# Đặt quyền ghi cho cache, logs, uploads
sudo chmod -R 777 application/cache/
sudo chmod -R 777 application/logs/
sudo chmod -R 777 uploads/
sudo chmod -R 777 asset/
```

---

## **9️⃣ SSL/TLS CERTIFICATE**
### Để bảo mật HTTPS

| # | Item | Giá trị | Ghi chú |
|---|------|--------|---------|
| 9.1 | SSL Provider | Let's Encrypt (Certbot) | Miễn phí |
| 9.2 | Domain | `pm.simplething.id.vn` | **CRITICAL** |
| 9.3 | Subdomain | `www.pm.simplething.id.vn` | Optional |
| 9.4 | Auto Renewal | Enabled | Cần enable cron job |
| 9.5 | Port HTTPS | `443` | Standard |

**Commands:**
```bash
sudo certbot --apache -d pm.simplething.id.vn -d www.pm.simplething.id.vn
```

---

## **🔟 FIREWALL CONFIGURATION**
### UFW Rules cần thiết

| # | Port | Protocol | Action | Ghi chú |
|---|------|----------|--------|---------|
| 10.1 | 22 | TCP | Allow | SSH |
| 10.2 | 80 | TCP | Allow | HTTP |
| 10.3 | 443 | TCP | Allow | HTTPS |
| 10.4 | 3306 | TCP | Allow (local) | MySQL |

**Commands:**
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

## **1️⃣1️⃣ BACKUP CONFIGURATION**
### Cấu hình backup tự động

**Backup Script:** `/usr/local/bin/backup-pm.sh`

```bash
#!/bin/bash
BACKUP_DIR="/backups/production-management"
MYSQL_USER="pm_user"
MYSQL_PASS="your_password"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup Database
mysqldump -u $MYSQL_USER -p$MYSQL_PASS db_production > $BACKUP_DIR/db_$DATE.sql

# Backup Files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/production-management-v2

# Cleanup old backups (>30 ngày)
find $BACKUP_DIR -type f -mtime +30 -delete
```

**Cron Job:** `0 2 * * * /usr/local/bin/backup-pm.sh`

---

## **1️⃣2️⃣ OPTIONAL CACHING**
### Cache servers (nếu muốn tối ưu performance)

| # | Service | Mục đích | Config |
|---|---------|---------|--------|
| 12.1 | Redis | Session/Query Cache | Port 6379 |
| 12.2 | Memcached | Query/Fragment Cache | Port 11211 |

**Nếu dùng Redis trong CodeIgniter:**
```php
// Trong application/config/config.php
$config['sess_driver'] = 'redis';
$config['sess_save_path'] = 'tcp://localhost:6379';
```

---

## **1️⃣3️⃣ MONITORING & LOGS**
### Monitoring cần thiết

| # | Item | Vị trí | Ghi chú |
|---|------|--------|---------|
| 13.1 | Apache Error Log | `/var/log/apache2/pm-error.log` | Theo dõi lỗi |
| 13.2 | Apache Access Log | `/var/log/apache2/pm-access.log` | Traffic logs |
| 13.3 | PHP Error Log | `/var/log/php/error.log` | Lỗi PHP |
| 13.4 | CodeIgniter Log | `application/logs/` | Log CI |
| 13.5 | MySQL Log | `/var/log/mysql/` | Log DB |

**Monitoring commands:**
```bash
# Xem lỗi real-time
sudo tail -f /var/log/apache2/pm-error.log

# Check PHP errors
sudo tail -f /var/log/php/error.log

# Check system resources
htop
df -h
free -h
```

---

## **📌 TÓNG KẾT - CÁC SETTING CẦN THAY ĐỔI**

### **🔴 CRITICAL (Phải thay):**
1. ✅ `index.php` - Đổi ENVIRONMENT thành `production`
2. ✅ `config.php` - Đổi `base_url` → `https://pm.simplething.id.vn/`
3. ✅ `database.php` - Đổi username (không dùng root) + password
4. ✅ MySQL - Tạo DB `db_production` + user `pm_user`
5. ✅ Firewall - Mở port 80, 443, 22

### **🟠 IMPORTANT (Nên thay):**
1. ✅ `config.php` - Encryption key mạnh
2. ✅ `config.php` - Đổi log_threshold thành 1
3. ✅ `config.php` - Cookie secure = true
4. ✅ `config.php` - CSRF protection = true
5. ✅ `database.php` - save_queries = false
6. ✅ `.htaccess` - Tạo file nếu chưa có
7. ✅ SSL/HTTPS - Cài đặt Certbot
8. ✅ PHP - Enable extensions (mysqli, mbstring, intl, json)

### **🟡 OPTIONAL (Tùy chọn):**
1. ✅ Redis/Memcached - Nếu muốn cache
2. ✅ Backup script - Tự động backup hàng ngày
3. ✅ `config.php` - Bật compress_output

---

## **📋 CHECKLIST BEFORE DEPLOY**

- [ ] Backup toàn bộ project từ local
- [ ] Backup database hiện tại
- [ ] Upload project lên server
- [ ] Sửa tất cả cấu hình trong danh sách này
- [ ] Tạo database và user MySQL mới
- [ ] Import SQL file vào database
- [ ] Kiểm tra quyền thư mục
- [ ] Enable Apache modules (rewrite, ssl)
- [ ] Kiểm tra PHP extensions
- [ ] Tạo VirtualHost Apache
- [ ] Cài đặt SSL certificate
- [ ] Test website hoạt động
- [ ] Kiểm tra logs có lỗi không
- [ ] Setup backup script
- [ ] Monitoring & log rotation

