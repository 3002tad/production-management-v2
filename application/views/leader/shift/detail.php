<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/shift'); ?>">Ca làm việc</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= $shift->shift_code ?></li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Chi Tiết Ca</h6>
        </nav>
    </div>
</nav>

<div class="container-fluid py-4">
    <!-- Shift Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><?= $shift->shift_name ?></h5>
                        <p class="text-sm mb-0"><?= $shift->shift_code ?></p>
                    </div>
                    <div>
                        <a href="<?= site_url('leader/shift/edit/' . $shift->shift_id) ?>" class="btn btn-sm btn-outline-dark">
                            <i class="material-icons text-sm">edit</i> Chỉnh sửa
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape icon-sm bg-gradient-primary shadow text-center border-radius-md me-2">
                                    <i class="material-icons opacity-10">domain</i>
                                </div>
                                <div>
                                    <h6 class="text-xs text-uppercase text-secondary mb-0">Khu vực</h6>
                                    <p class="text-sm font-weight-bold mb-0">
                                        <?php if (!empty($shift->zone_name)): ?>
                                            <?= $shift->zone_name ?> <span class="text-secondary">(<?= $shift->zone_code ?>)</span>
                                        <?php else: ?>
                                            <span class="text-secondary">Chưa phân khu</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape icon-sm bg-gradient-info shadow text-center border-radius-md me-2">
                                    <i class="material-icons opacity-10">view_timeline</i>
                                </div>
                                <div>
                                    <h6 class="text-xs text-uppercase text-secondary mb-0">Dây chuyền</h6>
                                    <p class="text-sm font-weight-bold mb-0">
                                        <?php if (!empty($shift->line_name)): ?>
                                            <?= $shift->line_name ?> <span class="text-secondary">(<?= $shift->line_code ?>)</span>
                                        <?php else: ?>
                                            <span class="text-secondary">Chưa phân dây chuyền</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-xs text-uppercase text-secondary mb-1">Ngày</h6>
                            <p class="text-sm font-weight-bold mb-0"><?= date('d/m/Y', strtotime($shift->shift_date)) ?></p>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-xs text-uppercase text-secondary mb-1">Giờ</h6>
                            <p class="text-sm font-weight-bold mb-0">
                                <?= date('H:i', strtotime($shift->start_time)) ?> - <?= date('H:i', strtotime($shift->end_time)) ?>
                            </p>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-xs text-uppercase text-secondary mb-1">Trạng thái</h6>
                            <?php
                            $status_badge_class = 'bg-gradient-secondary';
                            $status_text = 'Chưa bắt đầu';
                            if ($shift->shift_status == 2) {
                                $status_badge_class = 'bg-gradient-primary';
                                $status_text = 'Đang chạy';
                            } elseif ($shift->shift_status == 3) {
                                $status_badge_class = 'bg-gradient-success';
                                $status_text = 'Hoàn thành';
                            } elseif ($shift->shift_status == 4) {
                                $status_badge_class = 'bg-gradient-warning';
                                $status_text = 'Tạm dừng';
                            }
                            ?>
                            <span class="badge badge-sm <?= $status_badge_class ?>"><?= $status_text ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <ul class="nav nav-tabs" id="shiftTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="machine-tab" data-bs-toggle="tab" data-bs-target="#machine" type="button" role="tab" aria-controls="machine" aria-selected="true">
                                <i class="material-icons me-2">precision_manufacturing</i>Máy/Dây Chuyền
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab" aria-controls="staff" aria-selected="false">
                                <i class="material-icons me-2">people</i>Nhân Sự
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="shiftTabsContent">
                        <!-- Machine Tab - NEW LOGIC: Show machines by line with staff assignments -->
                        <div class="tab-pane fade show active" id="machine" role="tabpanel" aria-labelledby="machine-tab">
                            <div class="alert alert-info">
                                <i class="material-icons">info</i>
                                <strong>Lưu ý:</strong> Máy đã được gán cố định vào dây chuyền <strong><?= $shift->line_code ?></strong>. 
                                Chỉ cần phân công nhân sự vào từng máy.
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>Máy móc của dây chuyền <?= $shift->line_name ?> (<?= $shift->line_code ?>)</h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="loadMachinesByLine()">
                                    <i class="material-icons">refresh</i> Tải lại
                                </button>
                            </div>
                            
                            <!-- Machine Cards with Staff Assignments -->
                            <div class="row" id="machineCardsContainer">
                                <!-- Machines will be loaded here via AJAX -->
                                <div class="col-12 text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Tab (View Only) -->
                        <div class="tab-pane fade" id="staff" role="tabpanel" aria-labelledby="staff-tab">
                            <div class="alert alert-info">
                                <i class="material-icons">info</i>
                                <strong>Lưu ý:</strong> Danh sách nhân sự được gán từ các máy trong tab "Máy/Dây Chuyền". Đây chỉ là chế độ xem tổng quan.
                            </div>
                            <div class="mb-3">
                                <h6>Danh Sách Nhân Sự Đã Gán</h6>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0" id="staffTable">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nhân viên</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Vai trò</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Chức vụ</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Máy được gán</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($assigned_staff)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <i class="material-icons text-secondary" style="font-size: 48px;">person_off</i>
                                                    <p class="text-sm text-secondary mb-0">Chưa có nhân sự được gán vào máy</p>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($assigned_staff as $staff): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm"><?= $staff->staff_code ?></h6>
                                                                <p class="text-xs text-secondary mb-0"><?= $staff->full_name ?></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $role_badge = 'bg-gradient-info';
                                                        $role_text = 'Công nhân';
                                                        if ($staff->role_in_shift == 'leader') {
                                                            $role_badge = 'bg-gradient-primary';
                                                            $role_text = 'Trưởng ca';
                                                        } elseif ($staff->role_in_shift == 'qc') {
                                                            $role_badge = 'bg-gradient-success';
                                                            $role_text = 'QC';
                                                        } elseif ($staff->role_in_shift == 'technical') {
                                                            $role_badge = 'bg-gradient-warning';
                                                            $role_text = 'Kỹ thuật';
                                                        }
                                                        ?>
                                                        <span class="badge badge-sm <?= $role_badge ?>"><?= $role_text ?></span>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0"><?= $staff->department ?> - <?= $staff->position ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0"><?= $staff->machine_code ?? 'N/A' ?></p>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Machine Tab - NEW LOGIC: Show machines by line with staff assignments -->
                        <div class="tab-pane fade" id="machine" role="tabpanel" aria-labelledby="machine-tab">
                            <div class="alert alert-info">
                                <i class="material-icons">info</i>
                                <strong>Lưu ý:</strong> Máy đã được gán cố định vào dây chuyền <strong><?= $shift->line_code ?></strong>. 
                                Chỉ cần phân công nhân sự vào từng máy.
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>Máy móc của dây chuyền <?= $shift->line_name ?> (<?= $shift->line_code ?>)</h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="loadMachinesByLine()">
                                    <i class="material-icons">refresh</i> Tải lại
                                </button>
                            </div>
                            
                            <!-- Machine Cards with Staff Assignments -->
                            <div class="row" id="machineCardsContainer">
                                <!-- Machines will be loaded here via AJAX -->
                                <div class="col-12 text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-sm text-secondary mt-2">Đang tải danh sách máy...</p>
                                </div>
                            </div>

                            <!-- Breakdown History -->
                            <?php if (!empty($breakdown_logs)): ?>
                                <h6 class="mb-3">Lịch Sử Sự Cố Máy</h6>
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Máy cũ</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Máy thay thế</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lý do</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Người xử lý</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($breakdown_logs as $log): ?>
                                                <tr>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0"><?= date('d/m/Y H:i', strtotime($log->breakdown_time)) ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0"><?= $log->old_machine_code ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0"><?= $log->new_machine_code ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0"><?= $log->reason ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0"><?= $log->handled_by_username ?></p>
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
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStaffModalLabel">Thêm Nhân Sự Vào Ca</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addStaffForm" method="POST" action="<?= site_url('leader/shift/assign_staff'); ?>">
                <div class="modal-body">
                    <input type="hidden" name="shift_id" value="<?= $shift->shift_id ?>">
                    <div class="mb-3">
                        <label class="form-label">Chọn nhân viên</label>
                        <select name="staff_id" class="form-control" required id="staffSelect">
                            <option value="">-- Chọn --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vai trò trong ca</label>
                        <select name="role_in_shift" class="form-control" required>
                            <option value="worker">Công nhân</option>
                            <option value="leader">Trưởng ca</option>
                            <option value="qc">QC</option>
                            <option value="technical">Kỹ thuật</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Batch Assign Modal -->
<div class="modal fade" id="batchAssignModal" tabindex="-1" aria-labelledby="batchAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchAssignModalLabel">Phân Công Hàng Loạt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="batchAssignForm" method="POST" action="<?= site_url('leader/shift/batch_assign_staff'); ?>">
                <div class="modal-body">
                    <input type="hidden" name="shift_id" value="<?= $shift->shift_id ?>">
                    <div id="batchStaffContainer">
                        <div class="row mb-2 batch-staff-row">
                            <div class="col-md-5">
                                <select name="staff_ids[]" class="form-control batch-staff-select" required>
                                    <option value="">-- Chọn nhân viên --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="roles[]" class="form-control" required>
                                    <option value="worker">Công nhân</option>
                                    <option value="leader">Trưởng ca</option>
                                    <option value="qc">QC</option>
                                    <option value="technical">Kỹ thuật</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBatchStaffRow()">
                                    <i class="material-icons">add</i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Phân công</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Staff to Machine Modal -->
<div class="modal fade" id="assignStaffToMachineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignStaffModalTitle">Phân công nhân sự vào máy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= site_url('leader/shift/assign_staff_to_machine'); ?>">
                <div class="modal-body">
                    <input type="hidden" name="shift_id" value="<?= $shift->shift_id ?>">
                    <input type="hidden" name="machine_id" id="assignMachineId">
                    
                    <div class="mb-3">
                        <label class="form-label">Máy</label>
                        <input type="text" id="assignMachineName" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Loại máy</label>
                        <input type="text" id="assignMachineType" class="form-control" readonly>
                        <small class="text-muted" id="assignRoleHint"></small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Chọn nhân viên</label>
                        <select name="staff_id" class="form-control" required id="assignStaffSelect">
                            <option value="">-- Đang tải --</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú (tùy chọn)</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Phân công</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Handle Breakdown Modal -->
<div class="modal fade" id="breakdownModal" tabindex="-1" aria-labelledby="breakdownModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="breakdownModalLabel">Xử Lý Máy Hỏng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="breakdownForm" method="POST" action="<?= site_url('leader/shift/handle_breakdown'); ?>">
                <div class="modal-body">
                    <input type="hidden" name="assignment_id" id="breakdown_assignment_id">
                    <div class="alert alert-warning">
                        <i class="material-icons">warning</i>
                        Máy <strong id="breakdown_machine_code"></strong> đang bị hỏng. Chọn máy thay thế.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Máy thay thế</label>
                        <select name="new_machine_id" class="form-control" required id="replacementMachineSelect">
                            <option value="">-- Chọn máy --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning">Thay thế</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ==================== GLOBAL FUNCTIONS ====================

// NEW: Load machines by line on page load
function loadMachinesByLine() {
    const lineId = <?= $shift->line_id ?>;
    const shiftId = <?= $shift->shift_id ?>;
    
    $.ajax({
        url: '<?= site_url('leader/shift/get_machines_by_line'); ?>',
        method: 'POST',
        data: { line_id: lineId, shift_id: shiftId },
        dataType: 'json',
        success: function(response) {
            console.log('Machines Response:', response);
            if (response.success && response.data && response.data.length > 0) {
                displayMachineCards(response.data);
            } else {
                $('#machineCardsContainer').html(`
                    <div class="col-12 text-center py-4">
                        <i class="material-icons text-secondary" style="font-size: 48px;">precision_manufacturing</i>
                        <p class="text-sm text-secondary mb-0">Không có máy nào thuộc dây chuyền này</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Load Machines Error:', error);
            $('#machineCardsContainer').html(`
                <div class="col-12 text-center py-4">
                    <div class="alert alert-danger">Lỗi tải danh sách máy: ${error}</div>
                </div>
            `);
        }
    });
}

// Display machine cards with assigned staff
function displayMachineCards(machines) {
    let html = '';
    machines.forEach(function(machine) {
        const machineType = machine.equipment_category || 'production';
        const machineTypeText = machineType === 'quality_control' ? 'Kiểm định chất lượng (QC)' : 'Sản xuất';
        const badgeClass = machineType === 'quality_control' ? 'bg-warning' : 'bg-info';
        
        let staffList = '';
        if (machine.assigned_staff && machine.assigned_staff.length > 0) {
            machine.assigned_staff.forEach(function(staff) {
                staffList += `
                    <div class="d-flex align-items-center justify-content-between mb-2 border-bottom pb-2">
                        <div>
                            <p class="text-sm mb-0">${staff.full_name || staff.username}</p>
                            <small class="text-muted">${staff.role_name || 'N/A'} - ${staff.department || 'N/A'}</small>
                        </div>
                        <form method="POST" action="<?= site_url('leader/shift/remove_staff_from_machine'); ?>" style="display:inline;">
                            <input type="hidden" name="shift_id" value="<?= $shift->shift_id ?>">
                            <input type="hidden" name="assignment_id" value="${staff.id}">
                            <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Xóa phân công này?')">
                                <i class="material-icons text-sm">delete</i>
                            </button>
                        </form>
                    </div>
                `;
            });
        } else {
            staffList = '<p class="text-sm text-muted mb-0"><em>Chưa có nhân sự</em></p>';
        }
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-0">${machine.machine_code}</h6>
                                <p class="text-xs text-secondary mb-0">${machine.machine_name}</p>
                            </div>
                            <span class="badge ${badgeClass}">${machineTypeText}</span>
                        </div>
                        <p class="text-xs text-secondary mt-2 mb-0">Loại: ${machine.machine_type}</p>
                    </div>
                    <div class="card-body pt-2">
                        <h6 class="text-xs text-uppercase text-secondary mb-2">Nhân sự đã gán:</h6>
                        ${staffList}
                        <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-3" 
                                onclick="openAssignStaffModal(${machine.id}, '${machine.machine_code}', '${machine.machine_name}', '${machineType}', '${machineTypeText}')">
                            <i class="material-icons text-sm">person_add</i> Thêm nhân sự
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#machineCardsContainer').html(html);
}

// Open assign staff modal
function openAssignStaffModal(machineId, machineCode, machineName, machineType, machineTypeText) {
    console.log('Opening assign modal with:', { machineId, machineCode, machineName, machineType, machineTypeText });
    
    $('#assignMachineId').val(machineId);
    $('#assignMachineName').val(machineCode + ' - ' + machineName);
    $('#assignMachineType').val(machineTypeText);
    
    // Set role hint
    if (machineType === 'quality_control') {
        $('#assignRoleHint').html('<i class="material-icons text-sm">info</i> Chỉ hiển thị nhân viên QC');
    } else {
        $('#assignRoleHint').html('<i class="material-icons text-sm">info</i> Chỉ hiển thị công nhân (Worker)');
    }
    
    // Load staff by role
    console.log('Loading staff for machine_type:', machineType);
    $.ajax({
        url: '<?= site_url('leader/shift/get_staff_by_role'); ?>',
        method: 'POST',
        data: { machine_type: machineType },
        dataType: 'json',
        beforeSend: function() {
            $('#assignStaffSelect').html('<option value="">Đang tải...</option>');
        },
        success: function(response) {
            console.log('Staff by Role Response:', response);
            if (response.success && response.data && response.data.length > 0) {
                let options = '<option value="">-- Chọn nhân viên --</option>';
                response.data.forEach(function(staff) {
                    options += `<option value="${staff.user_id}">${staff.full_name || staff.username} (${staff.role_name})</option>`;
                });
                $('#assignStaffSelect').html(options);
                console.log('Loaded', response.data.length, 'staff members');
            } else {
                $('#assignStaffSelect').html('<option value="">Không có nhân viên phù hợp</option>');
                console.warn('No staff found. Response:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Load Staff AJAX Error:', { status, error, xhr });
            console.error('Response Text:', xhr.responseText);
            $('#assignStaffSelect').html('<option value="">Lỗi tải danh sách</option>');
        }
    });
    
    // Show modal using Bootstrap 5 API
    const modal = new bootstrap.Modal(document.getElementById('assignStaffToMachineModal'));
    modal.show();
}

// ==================== DOCUMENT READY ====================
document.addEventListener('DOMContentLoaded', function() {
    // Ensure jQuery is loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded!');
        alert('Lỗi: jQuery chưa được tải. Vui lòng kiểm tra kết nối internet.');
        return;
    }
    
    console.log('jQuery version:', jQuery.fn.jquery);
    
    // Load machines immediately on page load (Machine tab is default active)
    loadMachinesByLine();
    
    // Also load machines when Machine tab is clicked
    $('button[data-bs-target="#machine"]').on('shown.bs.tab', function (e) {
        loadMachinesByLine();
    });
    
    // Load available staff when modal opens
    $('#addStaffModal').on('show.bs.modal', function () {
    console.log('Modal opened, loading staff...');
    console.log('URL:', '<?= site_url('leader/shift/get_available_staff'); ?>');
    console.log('Shift ID:', <?= $shift->shift_id ?>);
    
    $.ajax({
        url: '<?= site_url('leader/shift/get_available_staff'); ?>',
        method: 'POST',
        data: { shift_id: <?= $shift->shift_id ?> },
        dataType: 'json',
        beforeSend: function() {
            console.log('Sending AJAX request...');
            $('#staffSelect').html('<option value="">Đang tải...</option>');
        },
        success: function(response) {
            console.log('Available Staff Response:', response);
            console.log('Response type:', typeof response);
            console.log('Response success:', response.success);
            console.log('Response data:', response.data);
            
            if (response.success && response.data && response.data.length > 0) {
                let options = '<option value="">-- Chọn --</option>';
                response.data.forEach(function(staff) {
                    console.log('Staff item:', staff);
                    options += `<option value="${staff.id_staff}">${staff.staff_code} - ${staff.full_name} (${staff.department} - ${staff.position})</option>`;
                });
                $('#staffSelect').html(options);
                console.log('Loaded', response.data.length, 'staff members');
            } else {
                $('#staffSelect').html('<option value="">Không có nhân sự khả dụng</option>');
                console.warn('No available staff found. Response:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error Details:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Status Code:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            console.error('Ready State:', xhr.readyState);
            $('#staffSelect').html('<option value="">Lỗi tải dữ liệu</option>');
        }
    });
});

// Load available staff for batch assign
$('#batchAssignModal').on('show.bs.modal', function () {
    loadBatchStaffOptions();
});

function loadBatchStaffOptions() {
    $.ajax({
        url: '<?= site_url('leader/shift/get_available_staff'); ?>',
        method: 'POST',
        data: { shift_id: <?= $shift->shift_id ?> },
        dataType: 'json',
        success: function(response) {
            console.log('Batch Staff Response:', response);
            if (response.success && response.data && response.data.length > 0) {
                let options = '<option value="">-- Chọn nhân viên --</option>';
                response.data.forEach(function(staff) {
                    options += `<option value="${staff.id_staff}">${staff.staff_code} - ${staff.full_name}</option>`;
                });
                $('.batch-staff-select').html(options);
            } else {
                $('.batch-staff-select').html('<option value="">Không có nhân sự khả dụng</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Batch AJAX Error:', status, error);
            $('.batch-staff-select').html('<option value="">Lỗi tải dữ liệu</option>');
        }
    });
}

function addBatchStaffRow() {
    const container = document.getElementById('batchStaffContainer');
    const newRow = document.createElement('div');
    newRow.className = 'row mb-2 batch-staff-row';
    newRow.innerHTML = `
        <div class="col-md-5">
            <select name="staff_ids[]" class="form-control batch-staff-select" required>
                <option value="">-- Chọn nhân viên --</option>
            </select>
        </div>
        <div class="col-md-4">
            <select name="roles[]" class="form-control" required>
                <option value="worker">Công nhân</option>
                <option value="leader">Trưởng ca</option>
                <option value="qc">QC</option>
                <option value="technical">Kỹ thuật</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBatchStaffRow(this)">
                <i class="material-icons">remove</i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    loadBatchStaffOptions();
}

function removeBatchStaffRow(button) {
    button.closest('.batch-staff-row').remove();
}

// OLD: Load available machines when modal opens (keep for backward compatibility)
$('#addMachineModal').on('show.bs.modal', function () {
    $.ajax({
        url: '<?= site_url('leader/shift/get_available_machines'); ?>',
        method: 'POST',
        data: { shift_id: <?= $shift->shift_id ?> },
        dataType: 'json',
        success: function(response) {
            console.log('Available Machines Response:', response);
            if (response.success && response.data && response.data.length > 0) {
                let options = '<option value="">-- Chọn --</option>';
                response.data.forEach(function(machine) {
                    options += `<option value="${machine.machine_id}">${machine.machine_code} - ${machine.machine_name} (${machine.machine_type})</option>`;
                });
                $('#machineSelect').html(options);
            } else {
                $('#machineSelect').html('<option value="">Không có máy khả dụng</option>');
                console.warn('No available machines found');
            }
        },
        error: function(xhr, status, error) {
            console.error('Machine AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            $('#machineSelect').html('<option value="">Lỗi tải dữ liệu</option>');
        }
    });
});

function openBreakdownModal(assignmentId, machineCode) {
    $('#breakdown_assignment_id').val(assignmentId);
    $('#breakdown_machine_code').text(machineCode);
    
    // Load replacement machines
    $.ajax({
        url: '<?= site_url('leader/shift/get_available_machines'); ?>',
        method: 'POST',
        data: { shift_id: <?= $shift->shift_id ?> },
        dataType: 'json',
        success: function(response) {
            console.log('Replacement Machines Response:', response);
            if (response.success && response.data && response.data.length > 0) {
                let options = '<option value="">-- Chọn máy --</option>';
                response.data.forEach(function(machine) {
                    options += `<option value="${machine.machine_id}">${machine.machine_code} - ${machine.machine_name}</option>`;
                });
                $('#replacementMachineSelect').html(options);
                $('#breakdownModal').modal('show');
            } else {
                alert('Không có máy khả dụng để thay thế');
            }
        },
        error: function(xhr, status, error) {
            console.error('Replacement Machine Error:', error);
            alert('Lỗi tải danh sách máy thay thế');
        }
    });
}

    function removeStaff(assignmentId) {
        if (confirm('Bạn có chắc muốn xóa nhân viên này khỏi ca?')) {
            window.location.href = '<?= site_url('leader/shift/remove_staff/'); ?>' + assignmentId;
        }
    }
    
    // Make functions globally accessible
    window.removeStaff = removeStaff;
    window.addBatchStaffRow = addBatchStaffRow;
    window.removeBatchStaffRow = removeBatchStaffRow;
    window.openBreakdownModal = openBreakdownModal;
    
}); // End document.ready
</script>
