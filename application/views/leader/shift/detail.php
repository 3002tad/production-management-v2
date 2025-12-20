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
                        <?php if ($shift->shift_status == 1): ?>
                            <!-- Chưa bắt đầu - Show Start button -->
                            <a href="<?= site_url('leader/start_shift/' . $shift->shift_id) ?>" 
                               class="btn btn-sm btn-success me-2"
                               onclick="return confirm('Xác nhận bắt đầu ca làm việc?')">
                                <i class="material-icons text-sm">play_arrow</i> Bắt đầu ca
                            </a>
                        <?php elseif ($shift->shift_status == 2 && $shift->is_closed != 1): ?>
                            <!-- Đang chạy và chưa chốt - Show End button -->
                            <a href="<?= site_url('leader/end_shift/' . $shift->shift_id) ?>" 
                               class="btn btn-sm btn-warning me-2">
                                <i class="material-icons text-sm">stop</i> Kết thúc & Chốt ca
                            </a>
                        <?php elseif ($shift->is_closed == 1): ?>
                            <!-- Đã chốt - Show view closure button -->
                            <button class="btn btn-sm btn-info me-2" disabled>
                                <i class="material-icons text-sm">check_circle</i> Đã chốt ca
                            </button>
                        <?php endif; ?>
                        
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
                            <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab" aria-controls="materials" aria-selected="false">
                                <i class="material-icons me-2">inventory_2</i>Nguyên Vật Liệu
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab" aria-controls="staff" aria-selected="false">
                                <i class="material-icons me-2">people</i>Nhân Sự
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="production-tab" data-bs-toggle="tab" data-bs-target="#production" type="button" role="tab" aria-controls="production" aria-selected="false">
                                <i class="material-icons me-2">assessment</i>Sản lượng
                                <span class="badge bg-success ms-2" id="productionRecordCount">0</span>
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

                        <!-- Materials Tab - Hiển thị nhu cầu NVL theo BOM/kế hoạch -->
                        <div class="tab-pane fade" id="materials" role="tabpanel" aria-labelledby="materials-tab">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Nhu cầu Nguyên Vật Liệu cho ca</h6>
                                    <?php $mc = isset($material_coverage) ? $material_coverage : null; ?>
                                    <?php if (!empty($mc['_meta'])): ?>
                                        <p class="text-sm text-secondary mb-0">
                                            Kế hoạch: <strong><?= htmlspecialchars($mc['_meta']['plan_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                            (ID: <?= (int) $mc['_meta']['plan_id']; ?>)
                                            &mdash; Số lượng tính toán: <strong><?= number_format($mc['_meta']['qty_for_calc']); ?></strong>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-sm text-secondary mb-0">Không tìm thấy thông tin kế hoạch hoặc sản phẩm để tính NVL.</p>
                                    <?php endif; ?>

                                    <?php if (isset($material_confirm) && $material_confirm): ?>
                                        <p class="text-xs text-success mb-0 mt-1">
                                            <i class="material-icons" style="font-size: 16px;">check_circle</i>
                                            <span class="ms-1">Đã xác nhận NVL lúc
                                                <strong><?= date('H:i d/m/Y', strtotime($material_confirm->confirmed_at)); ?></strong>
                                                <?php if (!empty($material_confirm->confirmed_username)): ?>
                                                    bởi <strong><?= htmlspecialchars($material_confirm->confirmed_username, ENT_QUOTES, 'UTF-8'); ?></strong>
                                                <?php endif; ?>
                                            </span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if (!empty($mc) && !empty($mc['details']) && (!isset($mc['ok']) || $mc['ok'])): ?>
                                        <?php if (isset($material_confirm) && $material_confirm): ?>
                                            <!-- Đã xác nhận - nút xanh, disabled -->
                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    disabled>
                                                <i class="material-icons text-sm me-1">check_circle</i>
                                                Đã xác nhận NVL
                                            </button>
                                        <?php elseif ($shift->shift_status == 2 || $shift->is_closed == 1): ?>
                                            <!-- Ca đã chạy hoặc đã chốt - nút bạc, disabled -->
                                            <button type="button"
                                                    class="btn btn-sm btn-secondary"
                                                    disabled
                                                    title="Ca đã bắt đầu, không thể xác nhận NVL">
                                                <i class="material-icons text-sm me-1">block</i>
                                                Không thể xác nhận
                                            </button>
                                        <?php else: ?>
                                            <!-- Chưa xác nhận, ca chưa chạy - nút xám, active -->
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    id="btn_confirm_material"
                                                    data-shift-id="<?= (int) $shift->shift_id; ?>">
                                                <i class="material-icons text-sm me-1">done_all</i>
                                                Xác nhận NVL
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (empty($mc) || empty($mc['details'])): ?>
                                <div class="alert alert-secondary">
                                    <i class="material-icons">info</i>
                                    <span class="ms-2">Chưa có dữ liệu BOM hoặc không thể tính nhu cầu NVL cho ca này.</span>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã NVL</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tên NVL</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Nhu cầu</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Khả dụng</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Thiếu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($mc['details'] as $row): ?>
                                                <?php
                                                    $required = isset($row['required_qty']) ? (float) $row['required_qty'] : 0;
                                                    $available = isset($row['available_qty']) ? (float) $row['available_qty'] : 0;
                                                    $shortage = isset($row['shortage']) ? (float) $row['shortage'] : 0;
                                                    $unit = isset($row['unit']) ? $row['unit'] : '';
                                                    $short_badge = $shortage > 0 ? 'text-danger font-weight-bold' : 'text-success';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($row['id_material'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs mb-0"><?= htmlspecialchars($row['material_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="text-xs font-weight-bold"><?= number_format($required, 2); ?> <?= htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?></span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="text-xs"><?= number_format($available, 2); ?> <?= htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?></span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="text-xs <?= $short_badge; ?>"><?= number_format($shortage, 2); ?> <?= htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
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

                        <!-- Production Tab - Display simulated production data -->
                        <div class="tab-pane fade" id="production" role="tabpanel" aria-labelledby="production-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1">Dữ liệu sản lượng sản xuất</h6>
                                    <p class="text-sm text-secondary mb-0">
                                        <span class="status-indicator" id="simulatorStatusIndicator"></span>
                                        <span id="simulatorStatusText">Đang kiểm tra trạng thái simulator...</span>
                                    </p>
                                </div>
                                <div>
                                    <a href="<?= site_url('simulator/settings'); ?>" class="btn btn-sm btn-outline-dark me-2" target="_blank">
                                        <i class="material-icons text-sm">settings</i> Cài đặt
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="loadProductionData()" id="refreshProductionBtn">
                                        <i class="material-icons text-sm">refresh</i> Làm mới
                                    </button>
                                </div>
                            </div>

                            <!-- Production Summary Cards -->
                            <div class="row mb-4" id="productionSummaryCards">
                                <div class="col-12 text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-sm text-secondary mt-2">Đang tải dữ liệu sản lượng...</p>
                                </div>
                            </div>

                            <!-- Production Records Table -->
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h6>Chi tiết bản ghi sản lượng</h6>
                                </div>
                                <div class="card-body px-0 pt-0 pb-2">
                                    <div class="table-responsive p-0">
                                        <table class="table align-items-center mb-0" id="productionRecordsTable">
                                            <thead>
                                                <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Máy</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nhân viên</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Thành phẩm</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Phế phẩm</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Mục tiêu</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Hiệu suất</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Downtime</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productionRecordsBody">
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <p class="text-sm text-secondary mb-0">Chưa có dữ liệu</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
                // Display staff name (prioritizes staff_name, falls back to user info)
                const staffNameDisplay = staff.staff_name || 'N/A';
                staffList += `
                    <div class="d-flex align-items-center justify-content-between mb-2 border-bottom pb-2">
                        <div>
                            <p class="text-sm mb-0">${staffNameDisplay}</p>
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

// ============================================
// Production Simulator Auto-Polling
// ============================================
let simulatorInterval = null;
let simulatorEnabled = false;
let simulatorIntervalSeconds = 300; // Default 5 minutes

// Check simulator status
function checkSimulatorStatus() {
    const url = '<?= site_url('simulator/status'); ?>';
    console.log('Checking simulator status at:', url);
    
    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                simulatorEnabled = data.enabled;
                simulatorIntervalSeconds = data.interval || 300;
                
                const indicator = document.getElementById('simulatorStatusIndicator');
                const statusText = document.getElementById('simulatorStatusText');
                
                if (simulatorEnabled) {
                    indicator.style.cssText = 'width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 8px; background-color: #4CAF50; animation: pulse 2s infinite;';
                    statusText.innerHTML = '<strong class="text-success">Simulator đang BẬT</strong> - Tự động ghi nhận sau mỗi ' + (simulatorIntervalSeconds / 60) + ' phút';
                    startSimulatorPolling();
                } else {
                    indicator.style.cssText = 'width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 8px; background-color: #9E9E9E;';
                    statusText.innerHTML = '<span class="text-secondary">Simulator đang TẮT</span> - <a href="<?= site_url('simulator/settings'); ?>" target="_blank">Bật tại đây</a>';
                    stopSimulatorPolling();
                }
            } else {
                // Migration not run or error
                const indicator = document.getElementById('simulatorStatusIndicator');
                const statusText = document.getElementById('simulatorStatusText');
                indicator.style.cssText = 'width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 8px; background-color: #FFC107;';
                statusText.innerHTML = '<span class="text-warning">Migration chưa chạy</span> - <a href="<?= site_url('simulator/settings'); ?>" target="_blank">Xem hướng dẫn</a>';
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking simulator status:', error);
            console.error('Status:', xhr.status);
            console.error('Response Text (first 500 chars):', xhr.responseText.substring(0, 500));
            
            const indicator = document.getElementById('simulatorStatusIndicator');
            const statusText = document.getElementById('simulatorStatusText');
            if (indicator && statusText) {
                indicator.style.cssText = 'width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 8px; background-color: #F44336;';
                if (xhr.status === 404) {
                    statusText.innerHTML = '<span class="text-danger">Endpoint không tìm thấy (404)</span>';
                } else {
                    statusText.innerHTML = '<span class="text-danger">Lỗi kết nối (' + xhr.status + ')</span>';
                }
            }
        }
    });
}

// Start auto-polling
function startSimulatorPolling() {
    if (simulatorInterval) {
        clearInterval(simulatorInterval);
    }
    
    // Run once immediately
    runSimulator();
    
    // Then run every interval
    simulatorInterval = setInterval(runSimulator, simulatorIntervalSeconds * 1000);
    
    console.log('Simulator polling started: every ' + simulatorIntervalSeconds + ' seconds');
}

// Stop auto-polling
function stopSimulatorPolling() {
    if (simulatorInterval) {
        clearInterval(simulatorInterval);
        simulatorInterval = null;
        console.log('Simulator polling stopped');
    }
}

// Run simulator
function runSimulator() {
    console.log('Running simulator at ' + new Date().toLocaleTimeString());
    
    fetch('<?= site_url('simulator/run'); ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Simulator run successful:', data);
            // Auto-refresh production data if on production tab
            const productionTab = document.getElementById('production-tab');
            if (productionTab && productionTab.classList.contains('active')) {
                loadProductionData();
            }
            // Update badge count
            updateProductionBadge();
        } else {
            console.log('Simulator not enabled or no data:', data.message);
        }
    })
    .catch(error => {
        console.error('Error running simulator:', error);
    });
}

// Load production data for this shift
function loadProductionData() {
    const shiftId = <?= $shift->shift_id ?>;
    const refreshBtn = document.getElementById('refreshProductionBtn');
    
    if (refreshBtn) {
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang tải...';
    }
    
    $.ajax({
        url: '<?= site_url('simulator/records/'); ?>' + shiftId,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                displayProductionSummary(data.summary);
                displayProductionRecords(data.records);
                updateProductionBadge(data.count);
            } else {
                document.getElementById('productionSummaryCards').innerHTML = 
                    '<div class="alert alert-info">Chưa có dữ liệu sản lượng</div>';
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading production data:', error);
            console.log('XHR Response:', xhr.responseText.substring(0, 200));
            document.getElementById('productionSummaryCards').innerHTML = 
                '<div class="alert alert-danger">Lỗi tải dữ liệu. Vui lòng refresh trang.</div>';
        },
        complete: function() {
            if (refreshBtn) {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = '<i class="material-icons text-sm">refresh</i> Làm mới';
            }
        }
    });
}

// Display production summary cards
function displayProductionSummary(summary) {
    const container = document.getElementById('productionSummaryCards');
    
    if (!summary || summary.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-info">Chưa có dữ liệu sản lượng</div></div>';
        return;
    }
    
    let html = '';
    summary.forEach(item => {
        const efficiencyColor = item.avg_efficiency >= 90 ? 'success' : (item.avg_efficiency >= 70 ? 'warning' : 'danger');
        const defectColor = item.avg_defect_rate <= 3 ? 'success' : (item.avg_defect_rate <= 5 ? 'warning' : 'danger');
        
        html += `
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="mb-2">${item.machine_code}</h6>
                        <p class="text-xs text-secondary mb-3">${item.machine_name}</p>
                        <div class="row">
                            <div class="col-6">
                                <p class="text-xs mb-1">Thành phẩm</p>
                                <h5 class="text-success mb-0">${item.total_good || 0}</h5>
                            </div>
                            <div class="col-6">
                                <p class="text-xs mb-1">Phế phẩm</p>
                                <h5 class="text-danger mb-0">${item.total_defect || 0}</h5>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <small class="text-${efficiencyColor}">Hiệu suất: ${parseFloat(item.avg_efficiency || 0).toFixed(1)}%</small>
                            <small class="text-${defectColor}">Tỷ lệ lỗi: ${parseFloat(item.avg_defect_rate || 0).toFixed(1)}%</small>
                        </div>
                        ${item.total_downtime > 0 ? `<small class="text-warning d-block mt-1">Downtime: ${item.total_downtime} phút</small>` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Display production records table
function displayProductionRecords(records) {
    const tbody = document.getElementById('productionRecordsBody');
    
    if (!records || records.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><p class="text-sm text-secondary mb-0">Chưa có bản ghi sản lượng</p></td></tr>';
        return;
    }
    
    let html = '';
    records.forEach(record => {
        const efficiencyClass = record.efficiency_rate >= 90 ? 'text-success' : (record.efficiency_rate >= 70 ? 'text-warning' : 'text-danger');
        const defectClass = record.defect_rate <= 3 ? 'text-success' : (record.defect_rate <= 5 ? 'text-warning' : 'text-danger');
        
        html += `
            <tr>
                <td class="text-xs">${new Date(record.timestamp).toLocaleString('vi-VN')}</td>
                <td class="text-xs"><strong>${record.machine_code}</strong><br><small class="text-secondary">${record.machine_name}</small></td>
                <td class="text-xs">${record.staff_name || '<span class="text-secondary">N/A</span>'}</td>
                <td class="text-center text-xs"><span class="badge bg-success">${record.good_count}</span></td>
                <td class="text-center text-xs"><span class="badge bg-danger">${record.defect_count}</span></td>
                <td class="text-center text-xs"><span class="badge bg-secondary">${record.target_count}</span></td>
                <td class="text-center text-xs ${efficiencyClass}"><strong>${parseFloat(record.efficiency_rate).toFixed(1)}%</strong></td>
                <td class="text-center text-xs">
                    ${record.downtime_minutes > 0 ? 
                        `<span class="badge bg-warning">${record.downtime_minutes}m</span><br><small class="text-secondary">${record.downtime_reason || ''}</small>` : 
                        '<span class="text-secondary">-</span>'}
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Update production badge count
function updateProductionBadge(count) {
    const badge = document.getElementById('productionRecordCount');
    if (badge) {
        if (count !== undefined) {
            badge.textContent = count;
        } else {
            // Fetch count
            const shiftId = <?= $shift->shift_id ?>;
            fetch('<?= site_url('simulator/records/'); ?>' + shiftId, {
                method: 'GET',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    badge.textContent = data.count || 0;
                }
            });
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check simulator status immediately
    checkSimulatorStatus();
    
    // Load production data if on production tab
    const productionTab = document.getElementById('production-tab');
    if (productionTab) {
        productionTab.addEventListener('shown.bs.tab', function() {
            loadProductionData();
        });
    }

    // Xác nhận NVL cho ca
    const confirmBtn = document.getElementById('btn_confirm_material');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            const shiftId = this.getAttribute('data-shift-id');
            if (!shiftId) return;

            if (!confirm('Xác nhận nhu cầu Nguyên Vật Liệu cho ca này?')) {
                return;
            }

            const btn = this;
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xác nhận...';

            fetch('<?= site_url('leader/shift/confirm_material'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: 'shift_id=' + encodeURIComponent(shiftId)
            })
            .then(function(resp) { return resp.json(); })
            .then(function(res) {
                if (res && res.success) {
                    alert(res.message || 'Đã xác nhận NVL cho ca');
                    // Change button to success state without reloading
                    btn.className = 'btn btn-sm btn-success';
                    btn.disabled = true;
                    btn.id = ''; // Remove ID so it won't be selected again
                    btn.innerHTML = '<i class="material-icons text-sm me-1">check_circle</i>Đã xác nhận NVL';
                } else {
                    alert(res && res.message ? res.message : 'Không thể xác nhận NVL');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(function(err) {
                console.error(err);
                alert('Có lỗi khi gửi yêu cầu xác nhận NVL');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    }
    
    // Re-check simulator status every minute
    setInterval(checkSimulatorStatus, 60000);
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    stopSimulatorPolling();
});

// Make function globally accessible
window.loadProductionData = loadProductionData;
</script>
