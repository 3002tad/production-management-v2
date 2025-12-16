<!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/machine/'); ?>">Máy/Dây chuyền</a></li>
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/machine/detail/' . $machine->id); ?>"><?= $machine->code ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Chỉnh sửa</li>
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

        <!-- Edit Machine Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <p class="mb-0">Chỉnh sửa thông tin máy: <strong><?= $machine->code ?></strong></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= site_url('leader/machine/update/' . $machine->id); ?>" id="editMachineForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-outline mb-3 is-filled">
                                        <label class="form-label">Mã máy (không thể thay đổi)</label>
                                        <input type="text" class="form-control" value="<?= $machine->code ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-outline mb-3 is-filled">
                                        <label class="form-label">Tên máy/dây chuyền *</label>
                                        <input type="text" class="form-control" name="name" value="<?= $machine->name ?>" required maxlength="100">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group input-group-outline mb-3 is-filled">
                                        <label class="form-label">Công suất (pieces/hour) *</label>
                                        <input type="number" class="form-control" name="capacity" value="<?= $machine->capacity ?>" required min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-outline mb-3 is-filled">
                                        <select class="form-control" name="stage_type" required>
                                            <option value="">-- Chọn loại công đoạn *</option>
                                            <option value="molding" <?= $machine->stage_type == 'molding' ? 'selected' : '' ?>>Ép khuôn (Molding)</option>
                                            <option value="assembly" <?= $machine->stage_type == 'assembly' ? 'selected' : '' ?>>Lắp ráp (Assembly)</option>
                                            <option value="packaging" <?= $machine->stage_type == 'packaging' ? 'selected' : '' ?>>Đóng gói (Packaging)</option>
                                            <option value="quality_check" <?= $machine->stage_type == 'quality_check' ? 'selected' : '' ?>>Kiểm tra chất lượng</option>
                                            <option value="other" <?= $machine->stage_type == 'other' ? 'selected' : '' ?>>Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-outline mb-3 is-filled">
                                        <select class="form-control" name="status" id="statusSelect">
                                            <option value="active" <?= $machine->status == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                            <option value="maintenance" <?= $machine->status == 'maintenance' ? 'selected' : '' ?>>Bảo trì</option>
                                            <option value="inactive" <?= $machine->status == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                                            <option value="broken" <?= $machine->status == 'broken' ? 'selected' : '' ?>>Hỏng</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status Change Reason -->
                            <div class="row" id="statusReasonRow" style="display: none;">
                                <div class="col-12">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Lý do thay đổi trạng thái</label>
                                        <input type="text" class="form-control" name="status_reason" maxlength="255" 
                                               placeholder="Nhập lý do thay đổi trạng thái máy...">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-outline mb-3 is-filled">
                                        <label class="form-label">Vị trí đặt máy</label>
                                        <input type="text" class="form-control" name="location" value="<?= $machine->location ?>" maxlength="100" 
                                               placeholder="VD: Khu A - Line 1">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3 is-filled">
                                        <label class="form-label">Ngày mua/lắp đặt</label>
                                        <input type="date" class="form-control" name="purchase_date" value="<?= $machine->purchase_date ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3 is-filled">
                                        <label class="form-label">Hết hạn bảo hành</label>
                                        <input type="date" class="form-control" name="warranty_until" value="<?= $machine->warranty_until ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-group input-group-outline mb-3 is-filled">
                                        <label class="form-label">Mô tả chi tiết</label>
                                        <textarea class="form-control" name="description" rows="3" 
                                                  placeholder="Mô tả chi tiết về máy, thông số kỹ thuật..."><?= $machine->description ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-warning" id="maintenanceWarning" style="display: none;">
                                        <i class="material-icons text-sm">warning</i>
                                        <strong>Cảnh báo:</strong> 
                                        Việc chuyển máy sang trạng thái bảo trì có thể ảnh hưởng đến các ca sản xuất đang hoạt động. 
                                        Vui lòng kiểm tra và điều chỉnh lịch sản xuất trước khi lưu.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <a href="<?= site_url('leader/machine/detail/' . $machine->id); ?>" class="btn btn-light me-2">
                                    <i class="material-icons text-sm">arrow_back</i>&nbsp;&nbsp;Hủy
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="material-icons text-sm">save</i>&nbsp;&nbsp;Lưu thay đổi
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
const originalStatus = '<?= $machine->status ?>';

// Monitor status changes
document.getElementById('statusSelect').addEventListener('change', function() {
    const newStatus = this.value;
    const reasonRow = document.getElementById('statusReasonRow');
    const maintenanceWarning = document.getElementById('maintenanceWarning');
    
    if (newStatus !== originalStatus) {
        reasonRow.style.display = 'block';
        reasonRow.querySelector('input').required = true;
        
        // Show warning when switching to maintenance from active
        if (originalStatus === 'active' && newStatus === 'maintenance') {
            maintenanceWarning.style.display = 'block';
        } else {
            maintenanceWarning.style.display = 'none';
        }
    } else {
        reasonRow.style.display = 'none';
        reasonRow.querySelector('input').required = false;
        maintenanceWarning.style.display = 'none';
    }
});

// Form validation and loading state
document.getElementById('editMachineForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const name = document.querySelector('input[name="name"]').value.trim();
    const capacity = document.querySelector('input[name="capacity"]').value;
    const stageType = document.querySelector('select[name="stage_type"]').value;
    const status = document.querySelector('select[name="status"]').value;
    
    // Basic validation
    if (!name || !capacity || !stageType || !status) {
        e.preventDefault();
        alert('⚠️ Vui lòng điền đầy đủ các trường bắt buộc (có dấu *)!');
        return false;
    }
    
    if (parseFloat(capacity) <= 0) {
        e.preventDefault();
        alert('⚠️ Công suất phải lớn hơn 0!');
        return false;
    }
    
    // Check status change reason
    if (status !== originalStatus) {
        const reason = document.querySelector('input[name="status_reason"]').value.trim();
        if (!reason) {
            e.preventDefault();
            alert('⚠️ Vui lòng nhập lý do thay đổi trạng thái!');
            return false;
        }
    }
    
    // Show loading state
    submitBtn.innerHTML = '<i class="material-icons text-sm">hourglass_empty</i>&nbsp;&nbsp;Đang cập nhật...';
    submitBtn.disabled = true;
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
