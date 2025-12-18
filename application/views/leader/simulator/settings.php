<style>
    .settings-card {
        transition: all 0.3s ease;
    }
    .settings-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .simulator-status-badge {
        font-size: 14px;
        padding: 8px 16px;
        border-radius: 20px;
    }
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>

<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/'); ?>">Leader</a></li>
                <li class="breadcrumb-item text-sm text-dark active">Production Simulator</li>
            </ol>
            <h6 class="font-weight-bolder mb-0"><?= $title ?></h6>
        </nav>
    </div>
</nav>

<div class="container-fluid py-4">
    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="material-icons">check_circle</i></span>
        <span class="alert-text"><?= $this->session->flashdata('success') ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Migration Warning -->
    <?php if (empty($settings) || !isset($settings['simulator_enabled'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="material-icons">warning</i></span>
        <div class="alert-text">
            <strong>Migration chưa chạy!</strong> Bảng dữ liệu simulator chưa được tạo.<br>
            Vui lòng chạy migration <code>016_create_production_simulator_tables.sql</code> trong phpMyAdmin.<br>
            <small>File: <code>db/migrations/Cap2/CaLamViec/016_create_production_simulator_tables.sql</code></small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Status Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-2">
                                <i class="material-icons text-lg" style="vertical-align: middle;">smart_toy</i>
                                Trạng thái Simulator
                            </h5>
                            <p class="text-sm text-secondary mb-0">
                                Hệ thống giả lập tự động ghi nhận sản lượng sản xuất theo máy trong ca
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="simulator-status-badge" id="statusBadge">
                                <span class="status-indicator" id="statusIndicator"></span>
                                <span id="statusText">Đang tải...</span>
                            </span>
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" id="simulatorToggle" 
                                       <?= $settings['simulator_enabled'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="simulatorToggle">
                                    <strong id="toggleLabel"><?= $settings['simulator_enabled'] ? 'BẬT' : 'TẮT' ?></strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <form id="settingsForm">
        <div class="row">
            <!-- General Settings -->
            <div class="col-md-6 mb-4">
                <div class="card settings-card h-100">
                    <div class="card-header pb-0">
                        <h6>
                            <i class="material-icons text-sm" style="vertical-align: middle;">settings</i>
                            Cài đặt chung
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Khoảng thời gian ghi nhận (giây)</label>
                            <input type="number" class="form-control" name="simulator_interval" 
                                   value="<?= $settings['simulator_interval'] ?>" min="60" max="3600" step="60">
                            <small class="form-text text-muted">
                                <i class="material-icons text-xs">info</i>
                                Simulator sẽ tự động ghi nhận sản lượng sau mỗi khoảng thời gian này
                            </small>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="simulate_active_shifts_only" value="0">
                            <input class="form-check-input" type="checkbox" name="simulate_active_shifts_only" 
                                   id="activeShiftsOnly" value="1" <?= $settings['simulate_active_shifts_only'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="activeShiftsOnly">
                                Chỉ giả lập cho ca đang chạy
                            </label>
                            <small class="form-text text-muted d-block">
                                Nếu bỏ chọn, sẽ giả lập cả ca "Planned"
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Production Settings -->
            <div class="col-md-6 mb-4">
                <div class="card settings-card h-100">
                    <div class="card-header pb-0">
                        <h6>
                            <i class="material-icons text-sm" style="vertical-align: middle;">inventory</i>
                            Sản lượng thành phẩm
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Số lượng tối thiểu</label>
                                <input type="number" class="form-control" name="good_count_min" 
                                       value="<?= $settings['good_count_min'] ?>" min="0" step="10">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Số lượng tối đa</label>
                                <input type="number" class="form-control" name="good_count_max" 
                                       value="<?= $settings['good_count_max'] ?>" min="0" step="10">
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="material-icons text-xs">info</i>
                            Số lượng thành phẩm sẽ được random trong khoảng này mỗi lần ghi
                        </small>
                    </div>
                </div>
            </div>

            <!-- Defect Settings -->
            <div class="col-md-6 mb-4">
                <div class="card settings-card h-100">
                    <div class="card-header pb-0">
                        <h6>
                            <i class="material-icons text-sm" style="vertical-align: middle;">warning</i>
                            Phế phẩm
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Tỷ lệ tối thiểu (%)</label>
                                <input type="number" class="form-control" name="defect_rate_min" 
                                       value="<?= $settings['defect_rate_min'] ?>" min="0" max="100" step="0.1">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Tỷ lệ tối đa (%)</label>
                                <input type="number" class="form-control" name="defect_rate_max" 
                                       value="<?= $settings['defect_rate_max'] ?>" min="0" max="100" step="0.1">
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="material-icons text-xs">info</i>
                            Tỷ lệ phế phẩm = (Số phế phẩm / Số thành phẩm) × 100%
                        </small>
                    </div>
                </div>
            </div>

            <!-- Downtime Settings -->
            <div class="col-md-6 mb-4">
                <div class="card settings-card h-100">
                    <div class="card-header pb-0">
                        <h6>
                            <i class="material-icons text-sm" style="vertical-align: middle;">schedule</i>
                            Thời gian ngừng máy (Downtime)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Xác suất xảy ra downtime</label>
                            <input type="number" class="form-control" name="downtime_probability" 
                                   value="<?= $settings['downtime_probability'] ?>" min="0" max="1" step="0.05">
                            <small class="form-text text-muted">Giá trị từ 0-1 (0.15 = 15% khả năng)</small>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Thời gian tối thiểu (phút)</label>
                                <input type="number" class="form-control" name="downtime_min" 
                                       value="<?= $settings['downtime_min'] ?>" min="0" step="5">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Thời gian tối đa (phút)</label>
                                <input type="number" class="form-control" name="downtime_max" 
                                       value="<?= $settings['downtime_max'] ?>" min="0" step="5">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Settings -->
            <div class="col-md-6 mb-4">
                <div class="card settings-card h-100">
                    <div class="card-header pb-0">
                        <h6>
                            <i class="material-icons text-sm" style="vertical-align: middle;">flag</i>
                            Mục tiêu sản lượng
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Hệ số nhân mục tiêu</label>
                            <input type="number" class="form-control" name="target_multiplier" 
                                   value="<?= $settings['target_multiplier'] ?>" min="1" max="2" step="0.1">
                            <small class="form-text text-muted">
                                <i class="material-icons text-xs">info</i>
                                Mục tiêu = Số thành phẩm × Hệ số này
                                <br>
                                Ví dụ: 100 sản phẩm × 1.2 = 120 target
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-sm text-secondary mb-0">
                                    <i class="material-icons text-xs">save</i>
                                    Nhấn "Lưu cài đặt" để áp dụng các thay đổi
                                </p>
                            </div>
                            <div>
                                <button type="button" class="btn btn-light me-2" onclick="location.reload()">
                                    <i class="material-icons text-sm">refresh</i>&nbsp;&nbsp;Làm mới
                                </button>
                                <button type="submit" class="btn btn-success" id="saveBtn">
                                    <i class="material-icons text-sm">save</i>&nbsp;&nbsp;Lưu cài đặt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const simulatorToggle = document.getElementById('simulatorToggle');
    const statusBadge = document.getElementById('statusBadge');
    const statusText = document.getElementById('statusText');
    const statusIndicator = document.getElementById('statusIndicator');
    const toggleLabel = document.getElementById('toggleLabel');
    const settingsForm = document.getElementById('settingsForm');
    
    // Update status display
    function updateStatusDisplay(enabled) {
        if (enabled) {
            statusBadge.className = 'simulator-status-badge bg-gradient-success text-white';
            statusIndicator.style.backgroundColor = '#4CAF50';
            statusText.textContent = 'ĐANG HOẠT ĐỘNG';
            toggleLabel.textContent = 'BẬT';
        } else {
            statusBadge.className = 'simulator-status-badge bg-gradient-secondary text-white';
            statusIndicator.style.backgroundColor = '#9E9E9E';
            statusText.textContent = 'TẮT';
            toggleLabel.textContent = 'TẮT';
        }
    }
    
    // Initialize status
    updateStatusDisplay(simulatorToggle.checked);
    
    // Toggle simulator on/off
    simulatorToggle.addEventListener('change', function() {
        const enabled = this.checked;
        
        fetch('<?= site_url('simulator/toggle'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'enabled=' + (enabled ? '1' : '0'),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatusDisplay(enabled);
                showNotification('success', data.message);
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Lỗi: ' + error.message);
            // Revert toggle
            simulatorToggle.checked = !enabled;
            updateStatusDisplay(!enabled);
        });
    });
    
    // Save settings
    settingsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
        
        const formData = new FormData(settingsForm);
        
        fetch('<?= site_url('simulator/update'); ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Lỗi: ' + error.message);
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="material-icons text-sm">save</i>&nbsp;&nbsp;Lưu cài đặt';
        });
    });
    
    // Show notification
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'check_circle' : 'error';
        
        const alertHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="material-icons">${iconClass}</i></span>
                <span class="alert-text">${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertAdjacentHTML('afterbegin', alertHTML);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
});
</script>
