# Hướng dẫn Deploy lên GitHub

## Bước 1: Khởi tạo Git Repository (Local)

Mở terminal/command prompt trong thư mục project:

```bash
cd d:\Code\PTUD\production-management-v2

# Khởi tạo git repository
git init

# Kiểm tra status
git status
```

## Bước 2: Add files vào staging

```bash
# Add tất cả files (trừ những file trong .gitignore)
git add .

# Kiểm tra files sẽ được commit
git status
```

## Bước 3: Commit lần đầu

```bash
# Commit với message
git commit -m "Initial commit: Production Management System v2.0.0"
```

## Bước 4: Tạo Repository trên GitHub

### Option A: Qua GitHub Website

1. Đăng nhập vào https://github.com
2. Click nút **"+"** góc phải trên → **"New repository"**
3. Điền thông tin:
   - **Repository name**: `production-management-v2`
   - **Description**: `Hệ thống Quản lý Sản xuất Bút bi - Production Management System`
   - **Visibility**: Chọn **Private** (nếu muốn riêng tư) hoặc **Public**
   - **KHÔNG** chọn "Initialize this repository with:"
     - ❌ README
     - ❌ .gitignore
     - ❌ license
4. Click **"Create repository"**

### Option B: Qua GitHub CLI (Nếu đã cài)

```bash
# Cài GitHub CLI: https://cli.github.com/

# Login
gh auth login

# Tạo repo
gh repo create production-management-v2 --private --source=. --remote=origin
```

## Bước 5: Kết nối Local với GitHub

GitHub sẽ hiển thị commands, copy và chạy:

```bash
# Thay [your-username] bằng username GitHub của bạn
git remote add origin https://github.com/[your-username]/production-management-v2.git

# Đổi tên branch main (nếu cần)
git branch -M main

# Push lên GitHub lần đầu
git push -u origin main
```

**Ví dụ:**
```bash
git remote add origin https://github.com/nguyenvana/production-management-v2.git
git branch -M main
git push -u origin main
```

## Bước 6: Xác thực GitHub

Khi push lần đầu, bạn sẽ được yêu cầu đăng nhập:

### Option 1: Personal Access Token (Khuyến nghị)

1. Vào GitHub → **Settings** → **Developer settings** → **Personal access tokens** → **Tokens (classic)**
2. Click **"Generate new token"** → **"Generate new token (classic)"**
3. Chọn scopes:
   - ✅ `repo` (Full control of private repositories)
   - ✅ `workflow` (nếu dùng GitHub Actions)
4. Click **"Generate token"**
5. **Copy token ngay** (chỉ hiển thị 1 lần!)
6. Khi Git yêu cầu password, paste token vào

### Option 2: GitHub Desktop

1. Download GitHub Desktop: https://desktop.github.com/
2. Mở GitHub Desktop
3. **File** → **Add Local Repository** → Chọn folder project
4. **Publish repository** → Chọn Private/Public → Publish

### Option 3: SSH Key (Advanced)

```bash
# Tạo SSH key
ssh-keygen -t ed25519 -C "your_email@example.com"

# Copy public key
cat ~/.ssh/id_ed25519.pub

# Thêm vào GitHub: Settings → SSH and GPG keys → New SSH key
# Paste nội dung file .pub

# Đổi remote URL sang SSH
git remote set-url origin git@github.com:[your-username]/production-management-v2.git
```

## Bước 7: Verify

Kiểm tra trên GitHub:
1. Vào https://github.com/[your-username]/production-management-v2
2. Xem files đã được upload chưa
3. Check README.md hiển thị đẹp không

## Bước 8: Setup Collaborators (Làm việc nhóm)

### Thêm thành viên vào repo:

1. Vào repo trên GitHub
2. **Settings** → **Collaborators and teams**
3. Click **"Add people"**
4. Nhập username/email của teammates
5. Chọn quyền:
   - **Read**: Chỉ xem
   - **Write**: Push code
   - **Admin**: Full quyền

### Teammates clone repo:

```bash
# Clone repo về máy
git clone https://github.com/[your-username]/production-management-v2.git

# Vào thư mục
cd production-management-v2

# Setup database và config theo README.md
```

## Bước 9: Branch Strategy (Làm việc nhóm)

### Branch Structure (3-tier)

```
main (Production)
  ↑
  └── test (Staging/Testing)
       ↑
       └── feature/... (Development)
```

### Main Branch (Protected - Production)

```bash
# KHÔNG code trực tiếp trên main!
# KHÔNG merge trực tiếp từ feature → main
# CHỈ merge từ test → main sau khi test ổn định
```

**Quy tắc:**
- Luôn stable, ready for production
- Chỉ nhận merge từ `test` branch
- Requires approval từ 2+ reviewers
- All tests must pass

### Test Branch (Staging/Testing)

```bash
# Branch để testing và QA
# Merge từ feature branches sau khi code review xong
# Test ổn định → merge vào main
```

**Quy tắc:**
- Nhận merge từ feature branches
- Dùng để test integration
- Phát hiện bugs trước khi production
- Requires approval từ 1+ reviewer

### Feature Branches (Development)

```bash
# Tạo branch mới cho feature từ test
git checkout test
git pull origin test
git checkout -b feature/ten-tinh-nang

# Làm việc trên branch này
# ... code code code ...

# Commit changes
git add .
git commit -m "feat: mô tả feature"

# Push lên GitHub
git push origin feature/ten-tinh-nang
```

**Quy tắc:**
- Luôn branch từ `test`, KHÔNG từ `main`
- Một feature = một branch
- Naming: `feature/`, `fix/`, `hotfix/`

### Pull Request Workflow

#### Step 1: Feature → Test (After Code Review)

1. Push feature branch lên GitHub
```bash
git push origin feature/ten-tinh-nang
```

2. Tạo PR: `feature/ten-tinh-nang` → `test`
   - Vào GitHub → **Pull requests** → **New pull request**
   - **base**: `test` ← **compare**: `feature/ten-tinh-nang`
   - Title: `feat: Thêm tính năng XYZ`
   - Điền mô tả chi tiết (features, screenshots)
   - Assign reviewers (teammates)
   - Add labels: `feature`, `needs-review`

3. Code Review
   - Reviewers comment và request changes
   - Developer fix và push updates
   - Approve sau khi ổn

4. Merge vào `test`
   - Click **"Merge pull request"**
   - Delete feature branch (optional)

#### Step 2: Testing Phase

```bash
# Pull test branch về
git checkout test
git pull origin test

# Deploy lên test server/local test
# Chạy toàn bộ test cases
# QA testing
# Bug fixes nếu cần
```

**Test Checklist:**
- [ ] Functional testing (features hoạt động đúng)
- [ ] Integration testing (modules tương tác OK)
- [ ] UI/UX testing (giao diện đẹp, responsive)
- [ ] Performance testing (tốc độ load)
- [ ] Security testing (SQL injection, XSS)
- [ ] Browser compatibility (Chrome, Firefox, Edge)
- [ ] Database migration OK
- [ ] No breaking changes

#### Step 3: Test → Main (After Testing Stable)

1. Tạo PR: `test` → `main`
   - **base**: `main` ← **compare**: `test`
   - Title: `release: Version X.Y.Z`
   - Mô tả:
     ```markdown
     ## Changes in this release
     - Feature A
     - Feature B
     - Bug fix C
     
     ## Testing Status
     - [x] All tests passed
     - [x] QA approved
     - [x] No critical bugs
     ```
   - Assign reviewers (2+ people, including tech lead)
   - Add labels: `release`, `critical`

2. Final Review
   - Tech lead/Senior review kỹ
   - Verify test results
   - Check for breaking changes

3. Merge vào `main`
   - **Squash and merge** (optional, gộp commits)
   - Tag version: `v2.1.0`
   - Update CHANGELOG.md

4. Deploy to Production
```bash
# Pull main branch
git checkout main
git pull origin main

# Deploy lên production server
# Monitor logs
# Rollback nếu có issues
```

## Bước 10: Daily Workflow

### Sáng: Update code mới nhất

```bash
# Chuyển về test branch (KHÔNG phải main!)
git checkout test

# Pull code mới nhất từ test
git pull origin test

# Tạo feature branch mới từ test
git checkout -b feature/my-work
```

### Trong ngày: Commit thường xuyên

```bash
# Xem thay đổi
git status
git diff

# Add và commit
git add .
git commit -m "feat: thêm validation cho form customer"

# Push lên để backup
git push origin feature/my-work
```

### Cuối ngày: Tạo Pull Request vào TEST

```bash
# Đảm bảo code đã push
git push origin feature/my-work

# Tạo PR trên GitHub: feature → test
# Base: test ← Compare: feature/my-work
# Assign reviewers
# Đợi feedback
```

### Sau khi Merge vào Test: Testing

```bash
# Pull test branch về
git checkout test
git pull origin test

# Test features trên local hoặc test server
# Fix bugs nếu có (tạo fix/* branches)
```

### Khi Test Ổn định: Release to Main

```bash
# Tạo PR: test → main
# Chỉ tech lead hoặc release manager làm bước này
# Sau khi approve → Merge vào main
# Tag version và deploy production
```

## Bước 11: Conflict Resolution

Nếu có conflict khi merge:

```bash
# Update test branch mới nhất (KHÔNG dùng main!)
git checkout test
git pull origin test

# Merge test vào feature branch
git checkout feature/my-work
git merge test

# Nếu có conflict, Git sẽ báo
# Mở files conflict và sửa thủ công
# Tìm các markers:
# <<<<<<< HEAD
# ... your code ...
# =======
# ... their code ...
# >>>>>>> test

# Sau khi sửa xong
git add .
git commit -m "fix: resolve merge conflicts with test branch"
git push origin feature/my-work
```

### Hotfix Workflow (Urgent bugs trên Production)

Nếu phát hiện bug nghiêm trọng trên `main`:

```bash
# Tạo hotfix branch từ main
git checkout main
git pull origin main
git checkout -b hotfix/fix-critical-bug

# Fix bug
# ... code fix ...

# Commit
git add .
git commit -m "hotfix: fix critical bug in production"

# Push
git push origin hotfix/fix-critical-bug

# Tạo 2 PRs:
# 1. hotfix → main (urgent, deploy ngay)
# 2. hotfix → test (sync fix vào test branch)
```

## Bước 12: Useful Git Commands

```bash
# Xem lịch sử commits
git log --oneline --graph --all

# Xem changes chưa commit
git diff

# Xem changes đã staged
git diff --staged

# Undo changes chưa commit
git checkout -- <file>

# Undo commit cuối (giữ changes)
git reset --soft HEAD~1

# Undo commit cuối (xóa changes)
git reset --hard HEAD~1

# Xem remote URL
git remote -v

# Xem tất cả branches
git branch -a

# Xóa branch local
git branch -d feature/old-branch

# Xóa branch remote
git push origin --delete feature/old-branch

# Stash changes tạm thời
git stash
git stash pop

# Tag version
git tag v2.0.0
git push origin v2.0.0
```

## Bước 13: GitHub Actions (CI/CD) - Optional

Tạo file `.github/workflows/ci.yml`:

```yaml
name: CI

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.0'
        extensions: mysqli, mbstring
        
    - name: Check PHP syntax
      run: find . -name "*.php" -exec php -l {} \;
```

## Bước 14: Protect Branches

### Protect Main Branch (Production)

1. GitHub repo → **Settings** → **Branches**
2. **Add rule** cho branch `main`:
   - ✅ Require pull request reviews before merging (2 reviewers)
   - ✅ Require status checks to pass
   - ✅ Require branches to be up to date
   - ✅ Include administrators (áp dụng cho cả admin)
   - ✅ Restrict who can push (chỉ tech lead/release manager)
3. Save

### Protect Test Branch (Staging)

1. **Add rule** cho branch `test`:
   - ✅ Require pull request reviews before merging (1 reviewer)
   - ✅ Require status checks to pass
   - ✅ Require branches to be up to date
2. Save

## Bước 15: Setup Test Branch

Nếu chưa có test branch, tạo ngay:

```bash
# Từ main branch
git checkout main
git pull origin main

# Tạo test branch
git checkout -b test

# Push lên GitHub
git push origin test

# Set as default branch cho development (optional)
# GitHub → Settings → Branches → Default branch → test
```

## Best Practices

### ✅ DO:
- **LUÔN branch từ `test`**, không từ `main`
- **Merge theo thứ tự**: feature → test → main
- **Test kỹ trên test branch** trước khi merge vào main
- Commit thường xuyên với messages rõ ràng
- Pull trước khi push
- Review code của teammates
- Viết documentation
- Update CHANGELOG.md khi release

### ❌ DON'T:
- **KHÔNG bao giờ merge trực tiếp** feature → main
- **KHÔNG code trực tiếp** trên main hoặc test
- **KHÔNG merge vào main** khi test chưa ổn định
- KHÔNG commit file config có password
- KHÔNG commit file lớn (>100MB)
- KHÔNG force push (`git push -f`) trên main/test
- KHÔNG commit code chưa test

### 🔄 Complete Workflow Summary

```
Developer A               Developer B              Tech Lead
─────────────────────────────────────────────────────────────
                                                   
1. feature/add-customer   feature/fix-bug          
   ↓ (code & commit)      ↓ (code & commit)
   
2. PR → test              PR → test                Review & Approve
   ↓ (after approval)     ↓ (after approval)
   
3. Merged to test ←───────┴──────────────────────→ Merge
   
4.                                                 Test on test branch
                                                   - QA Testing
                                                   - Integration test
                                                   - Bug fixes
                                                   
5.                                                 test → main PR
                                                   - Final review
                                                   - Tag version
                                                   
6.                                                 Deploy Production
                                                   - Monitor
                                                   - Rollback if needed
```

### 📊 Branch Naming Convention

```
feature/     - Tính năng mới
  ├── feature/add-product-filter
  ├── feature/export-excel
  └── feature/auto-fill-diameter

fix/         - Bug fixes (non-critical)
  ├── fix/validation-error
  └── fix/ui-alignment

hotfix/      - Critical bugs (production)
  ├── hotfix/security-vulnerability
  └── hotfix/data-loss-bug

refactor/    - Code refactoring
  └── refactor/database-queries

docs/        - Documentation only
  └── docs/update-readme

test/        - Experimental features
  └── test/new-framework
```

## Troubleshooting

### Lỗi: "fatal: remote origin already exists"
```bash
git remote remove origin
git remote add origin https://github.com/[username]/production-management-v2.git
```

### Lỗi: "error: failed to push some refs"
```bash
git pull origin main --rebase
git push origin main
```

### Lỗi: "Permission denied (publickey)"
```bash
# Dùng HTTPS thay vì SSH
git remote set-url origin https://github.com/[username]/production-management-v2.git
```

---

**Ready to push! 🚀**

Nếu có thắc mắc, tham khảo:
- GitHub Docs: https://docs.github.com
- Git Tutorial: https://git-scm.com/book/en/v2
