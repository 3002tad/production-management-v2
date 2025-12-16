<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <style>
        /* Hide left sidebar on change-password page and make content full width */
        #sidenav-main { display: none !important; }
        .main-content { margin-left: 0 !important; }
        body.g-sidenav-show { padding-left: 0 !important; }
    </style>
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Trang</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Đổi Mật Khẩu Bắt Buộc</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Đổi Mật Khẩu Bắt Buộc</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <h6 class="text-sm font-weight-bolder mb-0">Hệ Thống Quản Lý Sản Xuất</h6>
                <div class="col-6 d-flex text-end">
                    <a href="<?= site_url('login/logout'); ?>" class="btn gradient-dark mb-0" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">
                        <i class="material-icons">arrow_forward</i>
                        Đăng Xuất
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Đổi Mật Khẩu Bắt Buộc</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- User Info Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-gradient-primary">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-lg-2 col-md-3 text-center">
                                                <div class="avatar avatar-xl bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                                                    <i class="material-icons text-primary" style="font-size: 40px;">person</i>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-9">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <h6 class="text-white mb-1">Tên đăng nhập</h6>
                                                        <p class="text-white opacity-8 mb-0"><?= $this->session->userdata('username'); ?></p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <h6 class="text-white mb-1">Email</h6>
                                                        <p class="text-white opacity-8 mb-0"><?= $this->session->userdata('email'); ?></p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <h6 class="text-white mb-1">Vai trò</h6>
                                                        <p class="text-white opacity-8 mb-0"><?= $this->session->userdata('role_name'); ?></p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <h6 class="text-white mb-1">Bộ phận</h6>
                                                        <p class="text-white opacity-8 mb-0"><?php echo $this->session->userdata('department') ? $this->session->userdata('department') : 'Chưa xác định'; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Change Form -->
                        <div class="row">
                            <div class="col-12">
                                <form id="changePasswordForm" method="post" action="<?= site_url('login/change_password_process'); ?>">
                                    <?php if($this->session->flashdata('password_error')): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="material-icons">error</i>
                                            <?= $this->session->flashdata('password_error'); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="current_password" class="form-control-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                                            <i class="material-icons">visibility</i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="new_password" class="form-control-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới (6-11 ký tự)" required>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                                            <i class="material-icons">visibility</i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="password-strength"></div>
                                                <small class="text-muted d-block mt-1">• Kết hợp chữ hoa, chữ thường, số để mạnh hơn</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="confirm_password" class="form-control-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                                            <i class="material-icons">visibility</i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_passwords" name="show_passwords">
                                                    <label class="form-check-label" for="show_passwords">
                                                        Hiển thị tất cả mật khẩu
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="material-icons">save</i>
                                                Đổi Mật Khẩu
                                            </button>
                                            <a href="<?= site_url('login/logout'); ?>" class="btn btn-secondary btn-lg ml-2" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">
                                                <i class="material-icons">exit_to_app</i>
                                                Đăng Xuất
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Show success toast if flashdata exists
        <?php if($this->session->flashdata('success')): ?>
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: '<?= addslashes($this->session->flashdata('success')) ?>',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#17ad37',
                timer: 3000,
                timerProgressBar: true
            });
        }
        <?php endif; ?>

        // Toggle password visibility
        var toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var target = this.getAttribute('data-target');
                var input = document.getElementById(target);
                var icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.textContent = 'visibility_off';
                } else {
                    input.type = 'password';
                    icon.textContent = 'visibility';
                }
            });
        });

        // Show all passwords checkbox
        var showPasswordsCheckbox = document.getElementById('show_passwords');
        if (showPasswordsCheckbox) {
            showPasswordsCheckbox.addEventListener('change', function() {
                var show = this.checked;
                var inputs = document.querySelectorAll('input[type="password"], input[type="text"]');
                inputs.forEach(function(input) {
                    if (input.id !== 'current_password' && input.id !== 'new_password' && input.id !== 'confirm_password') return;

                    input.type = show ? 'text' : 'password';
                    var button = input.closest('.input-group').querySelector('.toggle-password i');
                    if (button) {
                        button.textContent = show ? 'visibility_off' : 'visibility';
                    }
                });
            });
        }

        // Password strength checker
        var newPasswordInput = document.getElementById('new_password');
        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', function() {
                var password = this.value;
                updatePasswordStrength(password);
            });

            newPasswordInput.addEventListener('keyup', function() {
                var password = this.value;
                updatePasswordStrength(password);
            });
        }

        // Update password strength display (match add user page logic)
        function updatePasswordStrength(password) {
            var strengthDiv = document.getElementById('password-strength');
            if (!strengthDiv) return;

            if (!password) {
                strengthDiv.innerHTML = '';
                return;
            }

            var strength = 0;
            var feedback = [];

            if (password.length >= 6) strength++;
            else feedback.push('ít nhất 6 ký tự');

            if (/[a-z]/.test(password)) strength++;
            else feedback.push('chữ thường');

            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('chữ hoa');

            if (/[0-9]/.test(password)) strength++;
            else feedback.push('số');

            strengthDiv.innerHTML = '';

            if (strength <= 2) {
                strengthDiv.innerHTML = '<div class="progress mt-1"><div class="progress-bar bg-danger" style="width: 25%"></div></div><small class="text-danger">Yếu: ' + feedback.join(', ') + '</small>';
            } else if (strength <= 3) {
                strengthDiv.innerHTML = '<div class="progress mt-1"><div class="progress-bar bg-warning" style="width: 50%"></div></div><small class="text-warning">Trung bình: ' + feedback.join(', ') + '</small>';
            } else if (strength <= 4) {
                strengthDiv.innerHTML = '<div class="progress mt-1"><div class="progress-bar bg-info" style="width: 75%"></div></div><small class="text-info">Khá: ' + feedback.join(', ') + '</small>';
            } else {
                strengthDiv.innerHTML = '<div class="progress mt-1"><div class="progress-bar bg-success" style="width: 100%"></div></div><small class="text-success">Mạnh</small>';
            }
        }

        // Form validation
        var form = document.getElementById('changePasswordForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                var newPassword = document.getElementById('new_password').value;
                var confirmPassword = document.getElementById('confirm_password').value;

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    Swal.fire({icon: 'error', title: 'Lỗi', text: 'Mật khẩu xác nhận không khớp!', confirmButtonText: 'Đóng'});
                    return false;
                }

                // Enforce stronger password policy: min 8 chars, include upper/lower/number/special
                var hasUpper = /[A-Z]/.test(newPassword);
                var hasLower = /[a-z]/.test(newPassword);
                var hasDigit = /[0-9]/.test(newPassword);
                var hasSpecial = /[^a-zA-Z0-9]/.test(newPassword);
                if (newPassword.length < 8 || !hasUpper || !hasLower || !hasDigit || !hasSpecial) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Mật khẩu yếu',
                        html: 'Mật khẩu phải tối thiểu 8 ký tự và bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.',
                        confirmButtonText: 'Đóng'
                    });
                    return false;
                }
            });
        }
    });
    </script>