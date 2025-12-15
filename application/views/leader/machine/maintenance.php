<style>
    .maintenance-card {
        transition: all 0.3s ease;
    }
    .maintenance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .status-planned { background: linear-gradient(195deg, #1A73E8 0%, #1662C4 100%); color: white; }
    .status-in_progress { background: linear-gradient(195deg, #FFA726 0%, #FB8C00 100%); color: white; }
    .status-completed { background: linear-gradient(195deg, #43A047 0%, #66BB6A 100%); color: white; }
    .status-cancelled { background: linear-gradient(195deg, #78909C 0%, #90A4AE 100%); color: white; }
</style>

<!-- Navbar -->
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">visibility</i>
                    </div>
                    <span class="nav-link-text ms-1">Chi tiết máy</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white active bg-gradient-primary" href="#">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">build_circle</i>
                    </div>
                    <span class="nav-link-text ms-1">Lịch bảo trì</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Main Content -->
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('machine/'); ?>">Máy/Dây chuyền</a></li>
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('machine/detail/' . $machine->id); ?>"><?= $machine->code ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Lịch bảo trì</li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= $title ?></h6>
            </nav>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success_js')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="material-icons">check_circle</i></span>
            <span class="alert-text">Thao tác thành công!</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('error_js')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="material-icons">error</i></span>
            <span class="alert-text">Có lỗi xảy ra, vui lòng kiểm tra lại!</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Machine Info Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6><?= $machine->name ?> (<?= $machine->code ?>)</h6>
                                <p class="text-sm mb-0">Quản lý lịch bảo trì máy/dây chuyền</p>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
                                <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Tạo lịch bảo trì
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Panel -->
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6>Lọc lịch bảo trì</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= site_url('machine/maintenance/' . $machine->id); ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group input-group-outline mb-3">
                                <select class="form-control" name="status">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    <option value="planned" <?= $filters['status'] == 'planned' ? 'selected' : '' ?>>Đã lên lịch</option>
                                    <option value="in_progress" <?= $filters['status'] == 'in_progress' ? 'selected' : '' ?>>Đang thực hiện</option>
                                    <option value="completed" <?= $filters['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                    <option value="cancelled" <?= $filters['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Từ ngày</label>
                                <input type="date" class="form-control" name="from_date" value="<?= $filters['from_date'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Đến ngày</label>
                                <input type="date" class="form-control" name="to_date" value="<?= $filters['to_date'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary mb-0">
                                <i class="material-icons">filter_list</i>&nbsp;&nbsp;Lọc
                            </button>
                            <a href="<?= site_url('machine/maintenance/' . $machine->id); ?>" class="btn btn-outline-secondary mb-0 ms-2">
                                <i class="material-icons">refresh</i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Maintenance Schedules -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Lịch bảo trì (<?= count($maintenance_schedules) ?> lịch)</h6>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <?php if (empty($maintenance_schedules)): ?>
                        <div class="text-center py-4">
                            <i class="material-icons" style="font-size: 48px; color: #ccc;">build_circle</i>
                            <p class="text-secondary mt-2">Chưa có lịch bảo trì nào</p>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
                                <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Tạo lịch bảo trì đầu tiên
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thông tin bảo trì</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Loại</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Chi phí</th>
                                        <th class="text-secondary opacity-7">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($maintenance_schedules as $maintenance): ?>
                                    <tr class="maintenance-card">
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <div class="icon icon-shape icon-sm shadow text-center me-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(195deg, #42424a 0%, #191919 100%);">
                                                        <i class="material-icons opacity-10" style="color: white;">build</i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm"><?= $maintenance->title ?></h6>
                                                    <p class="text-xs text-secondary mb-0"><?= $maintenance->description ?: 'Không có mô tả' ?></p>
                                                    <?php if ($maintenance->technician_name): ?>
                                                    <p class="text-xs text-info mb-0"><i class="material-icons text-xs">person</i> <?= $maintenance->technician_name ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0"><?= date('d/m/Y', strtotime($maintenance->start_time)) ?></p>
                                            <p class="text-xs text-secondary mb-0">
                                                <?= date('H:i', strtotime($maintenance->start_time)) ?> - <?= date('H:i', strtotime($maintenance->end_time)) ?>
                                            </p>
                                            <p class="text-xs text-info mb-0">
                                                <?php
                                                $duration = (strtotime($maintenance->end_time) - strtotime($maintenance->start_time)) / 3600;
                                                echo number_format($duration, 1) . ' giờ';
                                                ?>
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?php
                                            $type_labels = [
                                                'preventive' => 'Định kỳ',
                                                'corrective' => 'Sửa chữa',
                                                'emergency' => 'Khẩn cấp',
                                                'upgrade' => 'Nâng cấp'
                                            ];
                                            $type_colors = [
                                                'preventive' => 'bg-gradient-info',
                                                'corrective' => 'bg-gradient-warning',
                                                'emergency' => 'bg-gradient-danger',
                                                'upgrade' => 'bg-gradient-success'
                                            ];
                                            ?>
                                            <span class="badge badge-sm <?= $type_colors[$maintenance->maintenance_type] ?>">
                                                <?= $type_labels[$maintenance->maintenance_type] ?>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?php
                                            $status_labels = [
                                                'planned' => 'Đã lên lịch',
                                                'in_progress' => 'Đang thực hiện',
                                                'completed' => 'Hoàn thành',
                                                'cancelled' => 'Đã hủy'
                                            ];
                                            ?>
                                            <span class="badge badge-sm status-<?= $maintenance->status ?>">
                                                <?= $status_labels[$maintenance->status] ?>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?php if ($maintenance->estimated_cost > 0): ?>
                                            <p class="text-xs font-weight-bold mb-0">Ước tính: <?= number_format($maintenance->estimated_cost, 0, ',', '.') ?> VNĐ</p>
                                            <?php endif; ?>
                                            <?php if ($maintenance->actual_cost > 0): ?>
                                            <p class="text-xs text-success mb-0">Thực tế: <?= number_format($maintenance->actual_cost, 0, ',', '.') ?> VNĐ</p>
                                            <?php endif; ?>
                                            <?php if ($maintenance->estimated_cost == 0 && $maintenance->actual_cost == 0): ?>
                                            <span class="text-xs text-secondary">Chưa có</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <button class="btn btn-link text-primary text-gradient px-3 mb-0" onclick="viewMaintenance(<?= $maintenance->id ?>)">
                                                    <i class="material-icons text-sm me-2">visibility</i>Xem
                                                </button>
                                                <?php if (in_array($maintenance->status, ['planned', 'in_progress'])): ?>
                                                <button class="btn btn-link text-dark px-3 mb-0" onclick="editMaintenance(<?= $maintenance->id ?>)">
                                                    <i class="material-icons text-sm me-2">edit</i>Sửa
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Maintenance Modal -->
<div class="modal fade" id="addMaintenanceModal" tabindex="-1" aria-labelledby="addMaintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaintenanceModalLabel">Tạo lịch bảo trì mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= site_url('machine/createMaintenance/' . $machine->id); ?>" id="maintenanceForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Tiêu đề bảo trì *</label>
                                <input type="text" class="form-control" name="title" required maxlength="200">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Thời gian bắt đầu *</label>
                                <input type="datetime-local" class="form-control" name="start_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Thời gian kết thúc *</label>
                                <input type="datetime-local" class="form-control" name="end_time" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-3">
                                <select class="form-control" name="maintenance_type" required>
                                    <option value="">-- Chọn loại bảo trì *</option>
                                    <option value="preventive">Bảo trì định kỳ</option>
                                    <option value="corrective">Bảo trì sửa chữa</option>
                                    <option value="emergency">Bảo trì khẩn cấp</option>
                                    <option value="upgrade">Nâng cấp thiết bị</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Chi phí ước tính (VNĐ)</label>
                                <input type="number" class="form-control" name="estimated_cost" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Tên kỹ thuật viên</label>
                                <input type="text" class="form-control" name="technician_name" maxlength="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Mô tả chi tiết</label>
                                <textarea class="form-control" name="description" rows="3" 
                                          placeholder="Mô tả chi tiết về công việc bảo trì cần thực hiện..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea class="form-control" name="notes" rows="2" 
                                          placeholder="Ghi chú thêm (tùy chọn)..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="submitMaintenanceBtn">
                        <i class="material-icons text-sm">save</i>&nbsp;&nbsp;Tạo lịch bảo trì
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/material-dashboard.min.js?v=3.0.0'); ?>"></script>

<script>
// Form validation
document.getElementById('maintenanceForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitMaintenanceBtn');
    const title = document.querySelector('input[name="title"]').value.trim();
    const startTime = document.querySelector('input[name="start_time"]').value;
    const endTime = document.querySelector('input[name="end_time"]').value;
    const maintenanceType = document.querySelector('select[name="maintenance_type"]').value;
    
    // Basic validation
    if (!title || !startTime || !endTime || !maintenanceType) {
        e.preventDefault();
        alert('⚠️ Vui lòng điền đầy đủ các trường bắt buộc (có dấu *)!');
        return false;
    }
    
    // Time validation
    if (new Date(startTime) >= new Date(endTime)) {
        e.preventDefault();
        alert('⚠️ Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc!');
        return false;
    }
    
    // Must be future time
    if (new Date(startTime) <= new Date()) {
        e.preventDefault();
        alert('⚠️ Thời gian bắt đầu phải là thời gian trong tương lai!');
        return false;
    }
    
    // Show loading state
    submitBtn.innerHTML = '<i class="material-icons text-sm">hourglass_empty</i>&nbsp;&nbsp;Đang tạo...';
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

// View and edit functions (placeholders)
function viewMaintenance(id) {
    // TODO: Implement view maintenance details
    alert('Chức năng xem chi tiết bảo trì sẽ được triển khai trong phiên bản tiếp theo.');
}

function editMaintenance(id) {
    // TODO: Implement edit maintenance
    alert('Chức năng sửa lịch bảo trì sẽ được triển khai trong phiên bản tiếp theo.');
}
</script>