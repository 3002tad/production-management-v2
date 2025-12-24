<!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/machine/'); ?>">Máy/Dây chuyền</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Thêm mới</li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= $title ?></h6>
            </nav>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('error_js')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="material-icons">error</i></span>
            <span class="alert-text">Có lỗi xảy ra, vui lòng kiểm tra lại thông tin!</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Create Machine Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <p class="mb-0">Điền thông tin máy/dây chuyền mới</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= site_url('leader/machine/store'); ?>" id="createMachineForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-static mb-4">
                                        <label>Mã máy *</label>
                                        <input type="text" class="form-control" name="code" required maxlength="20" 
                                               placeholder="VD: ML001, AS001">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-static mb-4">
                                        <label>Tên máy/dây chuyền *</label>
                                        <input type="text" class="form-control" name="name" required maxlength="100">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group input-group-static mb-4">
                                        <label>Công suất (pieces/hour) *</label>
                                        <input type="number" class="form-control" name="capacity" required min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-static mb-4">
                                        <label>Loại công đoạn *</label>
                                        <select class="form-control" name="stage_type" required>
                                            <option value="">-- Chọn loại công đoạn *</option>
                                            <option value="molding">Ép khuôn (Molding)</option>
                                            <option value="assembly">Lắp ráp (Assembly)</option>
                                            <option value="packaging">Đóng gói (Packaging)</option>
                                            <option value="quality_check">Kiểm tra chất lượng</option>
                                            <option value="other">Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-static mb-4">
                                        <label>Trạng thái</label>
                                        <select class="form-control" name="status">
                                            <option value="active" selected>Hoạt động</option>
                                            <option value="maintenance">Bảo trì</option>
                                            <option value="inactive">Ngừng hoạt động</option>
                                            <option value="broken">Hỏng</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group input-group-static mb-4">
                                        <label>Vai trò máy</label>
                                        <select class="form-control" name="machine_role">
                                            <option value="primary" selected>Máy chính (Primary)</option>
                                            <option value="backup">Máy dự phòng (Backup)</option>
                                        </select>
                                        <small class="form-text text-muted">Máy chính: sử dụng thường xuyên. Máy dự phòng: thay thế khi máy chính gặp sự cố.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-static mb-4">
                                        <label>Dây chuyền <span id="lineHint" class="text-muted" style="font-weight:normal; font-size:12px;">(Bắt buộc cho máy chính, có thể để trống cho máy dự phòng)</span></label>
                                        <select class="form-control" name="line_id" id="line_id">
                                            <option value="">-- Chọn dây chuyền --</option>
                                            <?php foreach ($lines as $line): ?>
                                                <option value="<?= $line->id ?>">
                                                    <?= $line->line_name ?> (<?= $line->line_code ?>) 
                                                    <?php if (!empty($line->zone_name)): ?>
                                                        - <?= $line->zone_name ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-static mb-4">
                                        <label>Ngày mua/lắp đặt</label>
                                        <input type="date" class="form-control" name="purchase_date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-static mb-4">
                                        <label>Hết hạn bảo hành</label>
                                        <input type="date" class="form-control" name="warranty_until">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-group input-group-static mb-4">
                                        <label>Mô tả chi tiết về máy, thông số kỹ thuật...</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="material-icons text-sm">info</i>
                                        <strong>Lưu ý:</strong> 
                                        <ul class="mb-0 mt-2">
                                            <li>Mã máy phải là duy nhất trong hệ thống</li>
                                            <li>Công suất tính theo đơn vị pieces/hour (sản phẩm/giờ)</li>
                                            <li>Trạng thái ban đầu nên để "Hoạt động" nếu máy sẵn sàng</li>
                                            <li>Thông tin này sẽ được sử dụng trong module phân công ca sản xuất</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <a href="<?= site_url('leader/machine/'); ?>" class="btn btn-light me-2">
                                    <i class="material-icons text-sm">arrow_back</i>&nbsp;&nbsp;Hủy
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="material-icons text-sm">save</i>&nbsp;&nbsp;Lưu máy mới
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Core JS -->
<script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/material-dashboard.min.js?v=3.0.0'); ?>"></script>

<script>
// Form validation and loading state
document.getElementById('createMachineForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const code = document.querySelector('input[name="code"]').value.trim();
    const name = document.querySelector('input[name="name"]').value.trim();
    const capacity = document.querySelector('input[name="capacity"]').value;
    const stageType = document.querySelector('select[name="stage_type"]').value;
    
    // Basic validation
    if (!code || !name || !capacity || !stageType) {
        e.preventDefault();
        alert('⚠️ Vui lòng điền đầy đủ các trường bắt buộc (có dấu *)!');
        return false;
    }
    
    if (parseFloat(capacity) <= 0) {
        e.preventDefault();
        alert('⚠️ Công suất phải lớn hơn 0!');
        return false;
    }
    
    // Show loading state
    submitBtn.innerHTML = '<i class="material-icons text-sm">hourglass_empty</i>&nbsp;&nbsp;Đang lưu...';
    submitBtn.disabled = true;
});

// Toggle line requirement based on machine role
document.querySelector('select[name="machine_role"]').addEventListener('change', function(e) {
    const role = e.target.value;
    const lineSelect = document.getElementById('line_id');
    const lineHint = document.getElementById('lineHint');
    if (role === 'backup') {
        // backup machines may be unassigned to a line
        lineSelect.removeAttribute('required');
        lineHint.textContent = '(Không bắt buộc cho máy dự phòng)';
    } else {
        lineSelect.setAttribute('required', 'required');
        lineHint.textContent = '(Bắt buộc cho máy chính)';
    }
});

// Auto-hide alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        const closeBtn = alert.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.click();
        }
    });
}, 5000);
</script>
