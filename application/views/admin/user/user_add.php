<!-- UC6 - Gán Tài khoản cho Nhân viên -->
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item text-sm">
                        <a class="opacity-5 text-dark" href="<?= base_url('admin/') ?>">
                            <i class="material-icons text-sm">home</i>
                        </a>
                    </li>
                    <li class="breadcrumb-item text-sm">
                        <a class="opacity-5 text-dark" href="<?= base_url('admin/user') ?>">Người dùng</a>
                    </li>
                    <li class="breadcrumb-item text-sm active" aria-current="page">Gán Tài khoản</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Gán Tài khoản cho Nhân viên</h6>
            </nav>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header p-3 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="material-icons opacity-10" style="font-size: 24px; line-height: 40px;">person_add</i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Gán Tài khoản cho Nhân viên</h6>
                            <p class="text-sm mb-0">Chọn nhân viên và tạo thông tin đăng nhập</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <form action="<?= base_url('admin/user_add_process') ?>" method="POST" id="userAddForm" class="multisteps-form__form"
>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="staff_id" class="ms-0">Chọn nhân viên <span class="text-danger">*</span></label>
                                    <select class="form-control" id="staff_id" name="staff_id" required>
                                        <option value="">-- Chọn nhân viên --</option>
                                        <?php if (isset($staff_without_user) && !empty($staff_without_user)): ?>
                                            <?php foreach ($staff_without_user as $staff): ?>
                                            <option value="<?= $staff->id_staff ?>" 
                                                    data-name="<?= htmlspecialchars($staff->staff_name) ?>"
                                                    data-email="<?= htmlspecialchars($staff->email) ?>"
                                                    data-phone="<?= htmlspecialchars($staff->phone) ?>"
                                                    data-department="<?= htmlspecialchars($staff->department) ?>"
                                                    data-position="<?= htmlspecialchars($staff->position) ?>">
                                                <?= htmlspecialchars($staff->staff_name) ?> 
                                                <?php if (!empty($staff->department) && $staff->department != 'Chưa Phân Loại'): ?>
                                                    (<?= htmlspecialchars($staff->department) ?> - <?= htmlspecialchars($staff->position) ?>)
                                                <?php elseif (!empty($staff->email)): ?>
                                                    (<?= htmlspecialchars($staff->email) ?>)
                                                <?php endif; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>Không có nhân viên nào chưa có tài khoản</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <small class="text-muted d-block mt-n2 mb-3">
                                    <i class="material-icons text-xs">info</i> Chỉ nhân viên chưa có tài khoản mới có thể chọn
                                </small>
                            </div>

                            <!-- Thông tin nhân viên được chọn -->
                            <div class="col-md-6">
                                <div id="staffInfo" class="card bg-light" style="display: none;">
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-2">Thông tin nhân viên</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Bộ phận:</small><br>
                                                <span id="staffDepartment" class="fw-bold"></span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Chức vụ:</small><br>
                                                <span id="staffPosition" class="fw-bold"></span>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <small class="text-muted">Email:</small><br>
                                                <span id="staffEmail"></span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">SĐT:</small><br>
                                                <span id="staffPhone"></span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-success fw-bold" id="suggestedRole"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label class="ms-0">Username <span class="text-danger">*</span> <span class="text-muted">(Tối đa 11 ký tự)</span></label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           placeholder="VD: admin123, user_01, nguyenvan"
                                           required pattern="[a-zA-Z0-9_]+" maxlength="11">
                                </div>
                                <small class="text-muted d-block mt-n2 mb-3">
                                    <i class="material-icons text-xs">info</i> Chỉ chữ cái, số và gạch dưới (_)
                                </small>
                            </div>

                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label class="ms-0">Mật khẩu <span class="text-danger">*</span> <span class="text-muted">(6-11 ký tự)</span></label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Nhập mật khẩu từ 6-11 ký tự"
                                           required minlength="6" maxlength="11">
                                </div>
                                <div class="d-flex align-items-center mt-n2 mb-3">
                                    <small class="text-muted">
                                        <i class="material-icons text-xs">info</i> Kết hợp chữ hoa, chữ thường, số để mạnh hơn
                                    </small>
                                    <button class="btn btn-sm btn-link p-0 ms-auto" type="button" id="togglePassword">
                                        <i class="material-icons text-sm">visibility</i>
                                    </button>
                                </div>
                                <div id="passwordStrength"></div>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="role_id" class="ms-0">Vai trò <span class="text-danger">*</span></label>
                                    <select class="form-control" id="role_id" name="role_id" required>
                                        <option value="">-- Chọn vai trò --</option>
                                        <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role->role_id ?>">
                                            <?= $role->role_display_name ?> (Level: <?= $role->level ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-primary">
                                    <div class="d-flex">
                                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md me-3">
                                            <i class="material-icons opacity-10" style="font-size: 20px; line-height: 32px;">info</i>
                                        </div>
                                        <div>
                                            <h6 class="mb-2">Lưu ý quan trọng:</h6>
                                            <ul class="mb-0 text-sm">
                                                <li>Tài khoản sẽ được gán cho nhân viên đã chọn</li>
                                                <li>Sau khi tạo, user sẽ được yêu cầu <strong>đổi mật khẩu</strong> lần đầu đăng nhập</li>
                                                <li>Mật khẩu được lưu dạng <strong>plaintext</strong> (không mã hóa)</li>
                                                <li>Admin có thể reset mật khẩu bất cứ lúc nào từ danh sách user</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="<?= base_url('admin/user') ?>" class="btn btn-light me-2">
                                        <i class="material-icons text-sm">close</i> Hủy
                                    </a>
                                    <button type="submit" class="btn bg-gradient-primary">
                                        <i class="material-icons text-sm">check</i> Gán Tài khoản
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.input-group.input-group-static label {
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #344767;
    display: block;
}

.input-group.input-group-static label .text-danger {
    color: #dc3545 !important;
    font-weight: 700;
}

.input-group.input-group-static .form-control {
    border: 1px solid #d2d6da;
    border-radius: 0.375rem;
    padding: 0.625rem 0.75rem;
    font-size: 0.875rem;
}

.input-group.input-group-static .form-control:focus {
    border-color: #e91e63;
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
}
</style>

<script>
$(document).ready(function() {
    // Mapping từ position sang role_id
    const positionToRoleMap = {
        'Giám Đốc': '1', // bod
        'Trưởng Dây Chuyền': '2', // line_manager
        'Nhân Viên Kho': '3', // warehouse_staff
        'Nhân Viên QC': '5', // qc_staff
        'Kỹ Thuật Viên': '6', // technical_staff
        'Công Nhân': '7', // worker
        'Administrator': '4', // system_admin
        'Leader': '2' // line_manager
    };

    // Normalize string (remove diacritics, lowercase) for robust matching
    function normalizeText(str) {
        if (!str) return '';
        try {
            return str.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().trim();
        } catch (e) {
            // Fallback if normalize with Unicode property escapes isn't supported
            return str.replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
        }
    }

    // Build normalized map for faster lookup
    const normalizedPositionMap = {};
    Object.keys(positionToRoleMap).forEach(function(k) {
        normalizedPositionMap[normalizeText(k)] = positionToRoleMap[k];
    });

    // Xử lý khi chọn nhân viên - hỗ trợ cả select gốc và plugin (bootstrap-select)
    function handleStaffChange(el) {
        var $sel = $(el);
        var val = $sel.val();
        var selectedOption = $sel.find('option[value="' + val + '"]');
        var staffId = val;

        if (staffId) {
            var department = selectedOption.data('department') || 'Chưa Phân Loại';
            var position = selectedOption.data('position') || '';
            var email = selectedOption.data('email') || '';
            var phone = selectedOption.data('phone') || '';

            $('#staffDepartment').text(department || 'Chưa Phân Loại');
            $('#staffPosition').text(position || 'Chưa Phân Loại');
            $('#staffEmail').text(email);
            $('#staffPhone').text(phone);

            var normPosition = normalizeText(position);
            var suggestedRoleId = '';
            if (normalizedPositionMap[normPosition]) {
                suggestedRoleId = normalizedPositionMap[normPosition];
            } else {
                Object.keys(normalizedPositionMap).some(function(k) {
                    if (k && normPosition.indexOf(k) !== -1) {
                        suggestedRoleId = normalizedPositionMap[k];
                        return true;
                    }
                    return false;
                });
            }

            if (suggestedRoleId) {
                $('#role_id').val(suggestedRoleId);
                var roleText = $('#role_id option:selected').text();
                $('#suggestedRole').text('Gợi ý: ' + roleText);
            } else {
                $('#suggestedRole').text('Vui lòng chọn vai trò phù hợp');
            }

            $('#staffInfo').show();
        } else {
            $('#staffInfo').hide();
            $('#suggestedRole').text('');
        }
    }

    var $staffSelect = $('#staff_id');
    $staffSelect.on('change', function() { handleStaffChange(this); });
    // If bootstrap-select is used, it fires changed.bs.select - handle it too
    $staffSelect.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) { handleStaffChange(this); });

    // If a selectpicker plugin already initialized and has a selected value on load, trigger handler
    if ($staffSelect.val()) {
        handleStaffChange($staffSelect);
    }

    // Toggle password visibility
    $('#togglePassword').on('click', function() {
        var passwordInput = $('#password');
        var icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.text('visibility_off');
        } else {
            passwordInput.attr('type', 'password');
            icon.text('visibility');
        }
    });

    // Password strength indicator
    $('#password').on('input', function() {
        var password = $(this).val();
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

        var strengthBar = $('#passwordStrength');
        strengthBar.removeClass('bg-danger bg-warning bg-info bg-success');

        if (strength <= 2) {
            strengthBar.html('<div class="progress mt-1"><div class="progress-bar bg-danger" style="width: 25%"></div></div><small class="text-danger">Yếu: ' + feedback.join(', ') + '</small>');
        } else if (strength <= 3) {
            strengthBar.html('<div class="progress mt-1"><div class="progress-bar bg-warning" style="width: 50%"></div></div><small class="text-warning">Trung bình: ' + feedback.join(', ') + '</small>');
        } else if (strength <= 4) {
            strengthBar.html('<div class="progress mt-1"><div class="progress-bar bg-info" style="width: 75%"></div></div><small class="text-info">Khá: ' + feedback.join(', ') + '</small>');
        } else {
            strengthBar.html('<div class="progress mt-1"><div class="progress-bar bg-success" style="width: 100%"></div></div><small class="text-success">Mạnh</small>');
        }
    });

    // Form validation
    $('#userAddForm').on('submit', function(e) {
        var username = $('#username').val();
        var password = $('#password').val();
        var staffId = $('#staff_id').val();
        var roleId = $('#role_id').val();

        if (!staffId) {
            e.preventDefault();
            Swal.fire({icon: 'error', title: 'Lỗi', text: 'Vui lòng chọn nhân viên!', confirmButtonText: 'Đóng'});
            return false;
        }

        if (!roleId) {
            e.preventDefault();
            Swal.fire({icon: 'error', title: 'Lỗi', text: 'Vui lòng chọn vai trò!', confirmButtonText: 'Đóng'});
            return false;
        }

        if (username.length < 3 || username.length > 11) {
            e.preventDefault();
            Swal.fire({icon: 'error', title: 'Lỗi', text: 'Username phải từ 3-11 ký tự!', confirmButtonText: 'Đóng'});
            return false;
        }

        if (password.length < 6 || password.length > 11) {
            e.preventDefault();
            Swal.fire({icon: 'error', title: 'Lỗi', text: 'Mật khẩu phải từ 6-11 ký tự!', confirmButtonText: 'Đóng'});
            return false;
        }
    });

    // Safety: if jQuery/plugins load late, ensure handlers attached
    (function ensureInit() {
        if (typeof jQuery === 'undefined') {
            setTimeout(ensureInit, 200);
            return;
        }
        // Ensure password strength handler attached
        if ($('#password').length && !$('#password').data('strength-attached')) {
            $('#password').on('input', function() {
                var password = $(this).val();
                var strength = 0;
                var feedback = [];

                if (password.length >= 6) strength++; else feedback.push('ít nhất 6 ký tự');
                if (/[a-z]/.test(password)) strength++; else feedback.push('chữ thường');
                if (/[A-Z]/.test(password)) strength++; else feedback.push('chữ hoa');
                if (/[0-9]/.test(password)) strength++; else feedback.push('số');

                var strengthBar = $('#passwordStrength');
                strengthBar.removeClass('bg-danger bg-warning bg-info bg-success');

                if (strength <= 2) {
                    strengthBar.html('<div class="progress mt-1"><div class="progress-bar bg-danger" style="width: 25%"></div></div><small class="text-danger">Yếu: ' + feedback.join(', ') + '</small>');
                } else if (strength <= 3) {
                    strengthBar.html('<div class="progress mt-1"><div class="progress-bar bg-warning" style="width: 50%"></div></div><small class="text-warning">Trung bình: ' + feedback.join(', ') + '</small>');
                } else if (strength <= 4) {
                    strengthBar.html('<div class="progress mt-1"><div class="progress-bar bg-info" style="width: 75%"></div></div><small class="text-info">Khá: ' + feedback.join(', ') + '</small>');
                } else {
                    strengthBar.html('<div class="progress mt-1"><div class="progress-bar bg-success" style="width: 100%"></div></div><small class="text-success">Mạnh</small>');
                }
            });
            $('#password').data('strength-attached', true);
        }
    })();
});
</script>
