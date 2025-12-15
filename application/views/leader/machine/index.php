<style>
    .machine-card {
        transition: all 0.3s ease;
    }
    .machine-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
    .stage-molding { border-left: 4px solid #2196F3; }
    .stage-assembly { border-left: 4px solid #4CAF50; }
    .stage-packaging { border-left: 4px solid #FF9800; }
    .stage-quality_check { border-left: 4px solid #9C27B0; }
    .stage-other { border-left: 4px solid #607D8B; }
</style>

<!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('machine/'); ?>">Máy/Dây chuyền</a></li>
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
                            <p class="text-sm mb-0 text-capitalize">Tổng số máy</p>
                            <h4 class="mb-0"><?= $statistics['total_machines'] ?></h4>
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
                            <p class="text-sm mb-0 text-capitalize">Đang hoạt động</p>
                            <h4 class="mb-0"><?= $statistics['by_status']['active'] ?? 0 ?></h4>
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
                            <p class="text-sm mb-0 text-capitalize">Đang bảo trì</p>
                            <h4 class="mb-0"><?= $statistics['by_status']['maintenance'] ?? 0 ?></h4>
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
                            <p class="text-sm mb-0 text-capitalize">Bảo trì tháng này</p>
                            <h4 class="mb-0"><?= $statistics['monthly_maintenances'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Panel -->
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6>Tìm kiếm & Lọc</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= site_url('machine/'); ?>">
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
                                <input type="number" class="form-control" name="capacity_min" value="<?= $filters['capacity_min'] ?>" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Công suất tối đa</label>
                                <input type="number" class="form-control" name="capacity_max" value="<?= $filters['capacity_max'] ?>" min="0" step="0.01">
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

        <!-- Machines List - Grouped by Line and Area -->
        <div class="row">
            <div class="col-12">
                <?php if (empty($machines)): ?>
                <div class="card">
                    <div class="card-body text-center py-4">
                        <i class="material-icons" style="font-size: 48px; color: #ccc;">precision_manufacturing</i>
                        <p class="text-secondary mt-2">Không tìm thấy máy nào phù hợp với điều kiện lọc</p>
                    </div>
                </div>
                <?php else: ?>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Danh sách Máy/Dây chuyền (<?= $total ?> máy)</h5>
                    <?php if ($can_edit): ?>
                    <a href="<?= site_url('machine/create'); ?>" class="btn btn-primary btn-sm mb-0">
                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Thêm máy mới
                    </a>
                    <?php endif; ?>
                </div>

                <?php
                $status_labels = [
                    'active' => 'Hoạt động',
                    'maintenance' => 'Bảo trì',
                    'inactive' => 'Ngừng hoạt động', 
                    'broken' => 'Hỏng'
                ];
                
                $line_colors = [
                    'molding' => '#2196F3',
                    'assembly' => '#4CAF50',
                    'packaging' => '#FF9800',
                    'quality_check' => '#9C27B0',
                    'other' => '#607D8B'
                ];
                ?>

                <?php foreach ($grouped_machines as $line_key => $line_data): ?>
                <!-- Line Card -->
                <div class="card mb-3" style="border-left: 5px solid <?= $line_colors[$line_key] ?>;">
                    <div class="card-header" style="background: linear-gradient(195deg, <?= $line_colors[$line_key] ?>15 0%, <?= $line_colors[$line_key] ?>05 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="material-icons" style="vertical-align: middle; color: <?= $line_colors[$line_key] ?>;">settings_input_component</i>
                                <?= $line_data['label'] ?>
                            </h6>
                            <span class="badge bg-gradient-secondary">
                                <?= count($line_data['areas']) ?> khu • 
                                <?= array_sum(array_column($line_data['areas'], 'count')) ?> máy
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <?php foreach ($line_data['areas'] as $area_name => $area_data): ?>
                        <!-- Area Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background-color: #f8f9fa; border-radius: 6px;">
                                <h6 class="mb-0">
                                    <i class="material-icons text-sm" style="vertical-align: middle;">location_on</i>
                                    <?= $area_name ?>
                                </h6>
                                <div>
                                    <span class="badge badge-sm bg-gradient-success"><?= $area_data['active_count'] ?> hoạt động</span>
                                    <span class="badge badge-sm bg-gradient-secondary"><?= $area_data['count'] ?> tổng</span>
                                </div>
                            </div>

                            <!-- Machines in Area -->
                            <div class="row">
                                <?php foreach ($area_data['machines'] as $machine): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card machine-card h-100" style="border-left: 3px solid <?= $line_colors[$machine->stage_type] ?>;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="d-flex">
                                                    <div class="icon icon-shape icon-sm shadow text-center me-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(195deg, #42424a 0%, #191919 100%);">
                                                        <i class="material-icons opacity-10" style="color: white; font-size: 20px;">precision_manufacturing</i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?= $machine->code ?></h6>
                                                        <p class="text-sm text-secondary mb-1"><?= $machine->name ?></p>
                                                        <span class="badge badge-sm status-<?= $machine->status ?>"><?= $status_labels[$machine->status] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <p class="text-xs text-secondary mb-0">Công suất</p>
                                                    <p class="text-sm font-weight-bold mb-0"><?= number_format($machine->capacity, 0) ?> <span class="text-xs text-secondary">pieces/h</span></p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-xs text-secondary mb-0">Bảo trì</p>
                                                    <p class="text-sm font-weight-bold mb-0"><?= $machine->pending_maintenances ?> lịch</p>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end mt-3 pt-2" style="border-top: 1px solid #f0f0f0;">
                                                <a href="<?= site_url('machine/detail/' . $machine->id); ?>" class="btn btn-link text-primary text-gradient px-2 mb-0">
                                                    <i class="material-icons text-sm">visibility</i> CHI TIẾT
                                                </a>
                                                <?php if ($can_edit): ?>
                                                <a href="<?= site_url('machine/edit/' . $machine->id); ?>" class="btn btn-link text-dark px-2 mb-0">
                                                    <i class="material-icons text-sm">edit</i> SỬA
                                                </a>
                                                <?php endif; ?>
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

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= site_url('machine/?page=' . ($current_page - 1) . '&' . http_build_query($filters)) ?>">Trước</a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= site_url('machine/?page=' . $i . '&' . http_build_query($filters)) ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($current_page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= site_url('machine/?page=' . ($current_page + 1) . '&' . http_build_query($filters)) ?>">Sau</a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php endif; ?>
            </div>
        </div>
    </div>

<script>
// Auto-hide alerts after 5 seconds
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