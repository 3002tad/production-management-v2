<style>
    .machine-card {
        transition: all 0.3s ease;
    }
    .machine-card.hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.2) !important;
    }
    .status-active {
        background: linear-gradient(195deg, #43A047 0%, #66BB6A 100%);
        color: white;
    }
    .status-maintenance {
        background: linear-gradient(195deg, #FFA726 0%, #FB8C00 100%);
        color: white;
    }
    .status-inactive {
        background: linear-gradient(195deg, #78909C 0%, #90A4AE 100%);
        color: white;
    }
    .status-broken {
        background: linear-gradient(195deg, #E53935 0%, #EF5350 100%);
        color: white;
    }
    
    .card-header {
        position: relative;
        overflow: hidden;
    }
    
    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        pointer-events: none;
    }
</style>

<!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/machine/'); ?>">Máy/Dây chuyền</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Danh sách</li>
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10">precision_manufacturing</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Tổng Số Máy</p>
                            <h4 class="mb-0"><?= $total_machines ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10">play_circle</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Đang Hoạt Động</p>
                            <h4 class="mb-0"><?= $active_machines ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10">build</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Đang Bảo Trì</p>
                            <h4 class="mb-0"><?= $maintenance_machines ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10">calendar_month</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Hỏng Hóc</p>
                            <h4 class="mb-0"><?= $broken_machines ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mt-4 mb-3" id="machineTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="machines-tab" data-bs-toggle="tab" data-bs-target="#machines" type="button">
                    <i class="material-icons me-1" style="vertical-align: middle;">precision_manufacturing</i>
                    Danh sách Máy
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="zones-tab" data-bs-toggle="tab" data-bs-target="#zones" type="button">
                    <i class="material-icons me-1" style="vertical-align: middle;">domain</i>
                    Quản lý Khu vực
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="machineTabsContent">
            <!-- Machines Tab -->
            <div class="tab-pane fade show active" id="machines" role="tabpanel">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h6>Tìm kiếm & Lọc</h6>
                <a href="<?= site_url('leader/machine/create'); ?>" class="btn btn-primary btn-sm">
                    <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Thêm Máy Mới
                </a>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= site_url('leader/machine/'); ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Tìm kiếm...</label>
                                <input type="text" class="form-control" name="search" value="<?= $filters['search'] ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-outline mb-3">
                                <select class="form-control" name="status">
                                    <option value="">-- Trạng thái --</option>
                                    <option value="active" <?= $filters['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                    <option value="maintenance" <?= $filters['status'] == 'maintenance' ? 'selected' : '' ?>>Bảo trì</option>
                                    <option value="inactive" <?= $filters['status'] == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                                    <option value="broken" <?= $filters['status'] == 'broken' ? 'selected' : '' ?>>Hỏng</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-outline mb-3">
                                <select class="form-control" name="stage_type">
                                    <option value="">-- Loại công đoạn --</option>
                                    <option value="molding" <?= $filters['stage_type'] == 'molding' ? 'selected' : '' ?>>Ép khuôn</option>
                                    <option value="assembly" <?= $filters['stage_type'] == 'assembly' ? 'selected' : '' ?>>Lắp ráp</option>
                                    <option value="packaging" <?= $filters['stage_type'] == 'packaging' ? 'selected' : '' ?>>Đóng gói</option>
                                    <option value="quality_check" <?= $filters['stage_type'] == 'quality_check' ? 'selected' : '' ?>>Kiểm tra chất lượng</option>
                                    <option value="other" <?= $filters['stage_type'] == 'other' ? 'selected' : '' ?>>Khác</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Công suất tối thiểu</label>
                                <input type="number" class="form-control" name="capacity_min" value="<?= $filters['capacity_min'] ?? '' ?>" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Công suất tối đa</label>
                                <input type="number" class="form-control" name="capacity_max" value="<?= $filters['capacity_max'] ?? '' ?>" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary mb-0">
                                <i class="material-icons">search</i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Machines List - Grouped by Zone → Line → Machine -->
        <div class="row">
            <div class="col-12">
                <?php if (empty($machines_grouped)): ?>
                <div class="card">
                    <div class="card-body text-center py-4">
                        <i class="material-icons" style="font-size: 48px; color: #ccc;">precision_manufacturing</i>
                        <p class="text-secondary mt-2">Không có dữ liệu máy móc</p>
                    </div>
                </div>
                <?php else: ?>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Danh sách Máy (<?= $total_machines ?> máy)</h5>
                    <a href="<?= site_url('leader/machine/create'); ?>" class="btn btn-primary btn-sm mb-0">
                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Thêm Máy Mới
                    </a>
                </div>

                <?php
                $status_labels = [
                    'active' => 'Hoạt động',
                    'maintenance' => 'Bảo trì',
                    'inactive' => 'Ngừng hoạt động', 
                    'broken' => 'Hỏng'
                ];
                
                $status_colors = [
                    'active' => '#4CAF50',
                    'maintenance' => '#FF9800',
                    'inactive' => '#9E9E9E',
                    'broken' => '#F44336'
                ];
                
                $zone_colors = ['#2196F3', '#9C27B0', '#FF5722', '#00BCD4', '#4CAF50'];
                $color_index = 0;
                ?>

                <?php foreach ($machines_grouped as $zone_key => $zone): ?>
                <?php 
                    $zone_color = $zone_colors[$color_index % count($zone_colors)];
                    $color_index++;
                    
                    // Count machines in this zone
                    $zone_machine_count = 0;
                    foreach ($zone['lines'] as $line) {
                        $zone_machine_count += count($line['machines']);
                    }
                ?>
                
                <!-- Zone Card -->
                <div class="card mb-4" style="border-left: 5px solid <?= $zone_color ?>;">
                    <div class="card-header" style="background: linear-gradient(195deg, <?= $zone_color ?>15 0%, <?= $zone_color ?>05 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="material-icons" style="vertical-align: middle; color: <?= $zone_color ?>; font-size: 28px;">domain</i>
                                <strong><?= $zone['zone_name'] ?></strong>
                                <?php if (!empty($zone['description'])): ?>
                                <span class="text-sm text-secondary ms-2">(<?= $zone['description'] ?>)</span>
                                <?php endif; ?>
                            </h5>
                            <span class="badge bg-gradient-dark" style="font-size: 13px; padding: 8px 16px;">
                                <?= count($zone['lines']) ?> DÂY CHUYỀN • <?= $zone_machine_count ?> MÁY
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <?php foreach ($zone['lines'] as $line_key => $line): ?>
                        <?php
                            // Count active machines in this line
                            $line_active_count = 0;
                            foreach ($line['machines'] as $machine) {
                                if ($machine->status === 'active') {
                                    $line_active_count++;
                                }
                            }
                        ?>
                        
                        <!-- Production Line Section -->
                        <div class="mb-4 pb-3" style="border-bottom: 1px solid #e0e0e0;">
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3" style="background-color: #f8f9fa; border-radius: 8px; border-left: 3px solid <?= $zone_color ?>;">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="material-icons text-sm" style="vertical-align: middle; color: <?= $zone_color ?>;">settings_input_component</i>
                                        <strong><?= $line['line_name'] ?></strong>
                                    </h6>
                                    <p class="text-xs text-secondary mb-0">Mã: <?= $line['line_code'] ?></p>
                                </div>
                                <div>
                                    <span class="badge badge-sm bg-gradient-success"><?= $line_active_count ?> HOẠT ĐỘNG</span>
                                    <span class="badge badge-sm bg-gradient-secondary"><?= count($line['machines']) ?> TỔNG</span>
                                </div>
                            </div>

                            <!-- Machines in Line -->
                            <div class="row">
                                <?php foreach ($line['machines'] as $machine): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card machine-card h-100 hover-shadow" style="border-left: 3px solid <?= $status_colors[$machine->status] ?? '#ccc' ?>;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="d-flex">
                                                    <div class="icon icon-shape icon-sm shadow text-center me-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(195deg, <?= $status_colors[$machine->status] ?? '#ccc' ?> 0%, <?= $status_colors[$machine->status] ?? '#999' ?> 100%);">
                                                        <i class="material-icons opacity-10" style="color: white; font-size: 20px;">precision_manufacturing</i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?= $machine->code ?></h6>
                                                        <p class="text-sm text-secondary mb-0"><?= $machine->name ?></p>
                                                    </div>
                                                </div>
                                                <span class="badge badge-sm" style="background-color: <?= $status_colors[$machine->status] ?? '#ccc' ?>;">
                                                    <?= $status_labels[$machine->status] ?? 'N/A' ?>
                                                </span>
                                            </div>
                                            
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <p class="text-xs text-secondary mb-0">Công suất</p>
                                                    <p class="text-sm font-weight-bold mb-0">
                                                        <?= !empty($machine->capacity) ? number_format($machine->capacity, 0) . ' pc/h' : 'N/A' ?>
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-xs text-secondary mb-0">Loại</p>
                                                    <p class="text-sm font-weight-bold mb-0"><?= ucfirst($machine->stage_type ?? 'N/A') ?></p>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between mt-3 pt-2" style="border-top: 1px solid #f0f0f0;">
                                                <div>
                                                    <a href="<?= site_url('leader/machine/detail/' . $machine->id); ?>" class="btn btn-link text-primary text-gradient px-2 mb-0">
                                                        <i class="material-icons text-sm">visibility</i> CHI TIẾT
                                                    </a>
                                                </div>
                                                <div>
                                                    <button onclick="deleteMachine(<?= $machine->id ?>, '<?= $machine->code ?>')" class="btn btn-link text-danger px-2 mb-0" title="Xóa máy">
                                                        <i class="material-icons text-sm">delete</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php endif; ?>
            </div>
        </div>
            </div>
            <!-- End Machines Tab -->

            <!-- Zones Tab -->
            <div class="tab-pane fade" id="zones" role="tabpanel">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Quản lý Khu vực sản xuất</h6>
                        <button class="btn btn-primary btn-sm" onclick="openZoneModal()">
                            <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Thêm Khu Mới
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row" id="zonesContainer">
                            <?php if (empty($zones)): ?>
                            <div class="col-12 text-center py-5">
                                <i class="material-icons" style="font-size: 64px; color: #ccc;">domain</i>
                                <p class="text-secondary mt-3">Chưa có khu vực nào</p>
                            </div>
                            <?php else: ?>
                                <?php foreach ($zones as $zone): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100" style="border-left: 4px solid #2196F3;">
                                        <div class="card-header pb-2" style="background: linear-gradient(195deg, #2196F315 0%, #2196F305 100%);">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h6 class="mb-0">
                                                        <i class="material-icons text-sm" style="vertical-align: middle;">domain</i>
                                                        <?= $zone->zone_name ?>
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-0">Mã: <?= $zone->zone_code ?></p>
                                                </div>
                                                <span class="badge badge-sm <?= $zone->status == 1 ? 'bg-gradient-success' : 'bg-gradient-secondary' ?>">
                                                    <?= $zone->status == 1 ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <?php if (!empty($zone->description)): ?>
                                            <p class="text-sm text-secondary mb-2"><?= $zone->description ?></p>
                                            <?php endif; ?>
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="text-xs text-secondary mb-0">Tầng</p>
                                                    <p class="text-sm font-weight-bold mb-2"><?= $zone->floor ?: 'N/A' ?></p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-xs text-secondary mb-0">Tòa nhà</p>
                                                    <p class="text-sm font-weight-bold mb-2"><?= $zone->building ?: 'N/A' ?></p>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px solid #e0e0e0;">
                                                <div>
                                                    <span class="badge badge-sm bg-gradient-info"><?= $zone->line_count ?> Dây chuyền</span>
                                                    <span class="badge badge-sm bg-gradient-secondary"><?= $zone->machine_count ?> Máy</span>
                                                </div>
                                                <div>
                                                    <button onclick="editZone(<?= htmlspecialchars(json_encode($zone)) ?>)" class="btn btn-link text-dark px-1 mb-0" title="Sửa">
                                                        <i class="material-icons text-sm">edit</i>
                                                    </button>
                                                    <button onclick="deleteZone(<?= $zone->zone_id ?>, '<?= $zone->zone_name ?>', <?= $zone->line_count ?>)" class="btn btn-link text-danger px-1 mb-0" title="Xóa">
                                                        <i class="material-icons text-sm">delete</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Zones Tab -->
        </div>
    </div>
</div>

<!-- Zone Modal -->
<div class="modal fade" id="zoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zoneModalTitle">Thêm Khu vực</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="zoneForm">
                <input type="hidden" id="zone_id" name="zone_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-3">
                                <label>Mã khu *</label>
                                <input type="text" class="form-control" id="zone_code" name="zone_code" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-3">
                                <label>Tên khu *</label>
                                <input type="text" class="form-control" id="zone_name" name="zone_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-3">
                                <label>Tầng</label>
                                <input type="text" class="form-control" id="floor" name="floor">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-3">
                                <label>Tòa nhà</label>
                                <input type="text" class="form-control" id="building" name="building">
                            </div>
                        </div>
                    </div>
                    <div class="input-group input-group-static mb-3">
                        <label>Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    <div class="input-group input-group-static mb-3">
                        <label>Trạng thái</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1">Hoạt động</option>
                            <option value="0">Ngừng hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let zoneModal;

document.addEventListener('DOMContentLoaded', function() {
    zoneModal = new bootstrap.Modal(document.getElementById('zoneModal'));
    
    // Zone form submit
    document.getElementById('zoneForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveZone();
    });
});

function openZoneModal() {
    document.getElementById('zoneModalTitle').textContent = 'Thêm Khu vực';
    document.getElementById('zoneForm').reset();
    document.getElementById('zone_id').value = '';
    zoneModal.show();
}

function editZone(zone) {
    document.getElementById('zoneModalTitle').textContent = 'Chỉnh sửa Khu vực';
    document.getElementById('zone_id').value = zone.zone_id;
    document.getElementById('zone_code').value = zone.zone_code;
    document.getElementById('zone_name').value = zone.zone_name;
    document.getElementById('floor').value = zone.floor || '';
    document.getElementById('building').value = zone.building || '';
    document.getElementById('description').value = zone.description || '';
    document.getElementById('status').value = zone.status;
    zoneModal.show();
}

function saveZone() {
    const formData = new FormData(document.getElementById('zoneForm'));
    const zoneId = document.getElementById('zone_id').value;
    const url = zoneId ? 
        '<?= site_url('leader/machine/update_zone/') ?>' + zoneId : 
        '<?= site_url('leader/machine/save_zone') ?>';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    });
}

function deleteZone(zoneId, zoneName, lineCount) {
    if (lineCount > 0) {
        alert('Không thể xóa khu vực đang có ' + lineCount + ' dây chuyền. Vui lòng di chuyển hoặc xóa các dây chuyền trước.');
        return;
    }
    
    if (confirm('Bạn có chắc chắn muốn xóa khu vực "' + zoneName + '"?')) {
        fetch('<?= site_url('leader/machine/delete_zone/') ?>' + zoneId, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa khu vực');
        });
    }
}

// Delete machine function
function deleteMachine(machineId, machineCode) {
    if (confirm('Bạn có chắc chắn muốn xóa máy "' + machineCode + '"?\n\nCảnh báo: Thao tác này sẽ xóa tất cả dữ liệu liên quan (lịch bảo trì, log trạng thái). Không thể hoàn tác!')) {
        fetch('<?= site_url('leader/machine/delete/') ?>' + machineId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa máy');
        });
    }
}

    alerts.forEach(function(alert) {
        const closeBtn = alert.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.click();
        }
    });
}, 5000);
</script>
