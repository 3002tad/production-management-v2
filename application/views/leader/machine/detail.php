<style>
    .status-active { background: linear-gradient(195deg, #43A047 0%, #66BB6A 100%); color: white; }
    .status-maintenance { background: linear-gradient(195deg, #FFA726 0%, #FB8C00 100%); color: white; }
    .status-inactive { background: linear-gradient(195deg, #78909C 0%, #90A4AE 100%); color: white; }
    .status-broken { background: linear-gradient(195deg, #E53935 0%, #EF5350 100%); color: white; }
    .info-card {
        border-left: 4px solid #1A73E8;
        background: linear-gradient(195deg, rgba(26, 115, 232, 0.05) 0%, rgba(22, 98, 196, 0.05) 100%);
    }
</style>

<!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/machine/'); ?>">Máy/Dây chuyền</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= $machine->code ?></li>
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
            <span class="alert-text">Cập nhật thành công!</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Machine Info Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card info-card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6><?= $machine->name ?></h6>
                                <p class="text-sm mb-0">
                                    <strong>Mã máy:</strong> <?= $machine->code ?> | 
                                    <strong>Loại:</strong> <?php
                                    $stage_labels = [
                                        'molding' => 'Ép khuôn',
                                        'assembly' => 'Lắp ráp', 
                                        'packaging' => 'Đóng gói',
                                        'quality_check' => 'Kiểm tra CL',
                                        'other' => 'Khác'
                                    ];
                                    echo $stage_labels[$machine->stage_type];
                                    ?>
                                </p>
                            </div>
                            <div class="text-end">
                                <?php
                                $status_labels = [
                                    'active' => 'Hoạt động',
                                    'maintenance' => 'Bảo trì',
                                    'inactive' => 'Ngừng hoạt động', 
                                    'broken' => 'Hỏng'
                                ];
                                ?>
                                <span class="badge badge-lg status-<?= $machine->status ?>"><?= $status_labels[$machine->status] ?></span>
                                <p class="text-xs text-secondary mb-0 mt-1">
                                    Cập nhật: <?= date('d/m/Y H:i', strtotime($machine->updated_at)) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="material-icons" style="font-size: 48px; color: #1A73E8;">precision_manufacturing</i>
                                    <h6 class="mt-2">Công suất</h6>
                                    <h4 class="text-primary"><?= number_format($machine->capacity, 0) ?></h4>
                                    <p class="text-xs text-secondary">pieces/hour</p>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-sm mb-1"><i class="material-icons text-sm">domain</i> <strong>Khu vực:</strong></p>
                                        <p class="text-xs text-secondary">
                                            <?php if (!empty($machine->zone_name)): ?>
                                                <?= $machine->zone_name ?> (<?= $machine->zone_code ?>)
                                            <?php else: ?>
                                                Chưa phân khu
                                            <?php endif; ?>
                                        </p>
                                        
                                        <p class="text-sm mb-1 mt-3"><i class="material-icons text-sm">settings_input_component</i> <strong>Dây chuyền:</strong></p>
                                        <p class="text-xs text-secondary">
                                            <?php if (!empty($machine->line_name)): ?>
                                                <?= $machine->line_name ?> (<?= $machine->line_code ?>)
                                            <?php else: ?>
                                                Chưa phân dây chuyền
                                            <?php endif; ?>
                                        </p>
                                        
                                        <p class="text-sm mb-1 mt-3"><i class="material-icons text-sm">calendar_today</i> <strong>Ngày mua:</strong></p>
                                        <p class="text-xs text-secondary"><?= $machine->purchase_date ? date('d/m/Y', strtotime($machine->purchase_date)) : 'Chưa cập nhật' ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-sm mb-1"><i class="material-icons text-sm">shield</i> <strong>Bảo hành đến:</strong></p>
                                        <p class="text-xs text-secondary"><?= $machine->warranty_until ? date('d/m/Y', strtotime($machine->warranty_until)) : 'Chưa cập nhật' ?></p>
                                        
                                        <p class="text-sm mb-1 mt-3"><i class="material-icons text-sm">person</i> <strong>Người tạo:</strong></p>
                                        <p class="text-xs text-secondary"><?= $machine->created_by ?: 'Hệ thống' ?></p>
                                    </div>
                                </div>
                                <?php if ($machine->description): ?>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <p class="text-sm mb-1"><i class="material-icons text-sm">description</i> <strong>Mô tả:</strong></p>
                                        <p class="text-xs text-secondary"><?= $machine->description ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($can_edit): ?>
                        <div class="d-flex justify-content-end mt-3">
                            <a href="<?= site_url('leader/machine/edit/' . $machine->id); ?>" class="btn btn-primary btn-sm">
                                <i class="material-icons text-sm me-2">edit</i>Chỉnh sửa thông tin
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Status History -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Lịch sử thay đổi trạng thái</h6>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <?php if (empty($status_logs)): ?>
                        <div class="text-center py-4">
                            <i class="material-icons" style="font-size: 48px; color: #ccc;">history</i>
                            <p class="text-secondary mt-2">Chưa có lịch sử thay đổi nào</p>
                        </div>
                        <?php else: ?>
                        <div class="timeline timeline-one-side">
                            <?php foreach ($status_logs as $log): ?>
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    <i class="material-icons text-success text-gradient">radio_button_checked</i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">
                                        <?= $log->old_status ? $status_labels[$log->old_status] . ' → ' : '' ?>
                                        <?= $status_labels[$log->new_status] ?>
                                    </h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        <?= date('d/m/Y H:i', strtotime($log->changed_at)) ?>
                                    </p>
                                    <p class="text-sm mt-1 mb-0"><?= $log->reason ?></p>
                                    <?php if ($log->changed_by_username): ?>
                                    <p class="text-xs text-secondary mb-0">
                                        <i class="material-icons text-xs">person</i> <?= $log->changed_by_username ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Maintenance Schedule -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h6>Lịch bảo trì</h6>
                            <?php if ($can_manage_maintenance): ?>
                            <a href="<?= site_url('leader/machine/maintenance/' . $machine->id); ?>" class="btn btn-primary btn-sm">
                                <i class="material-icons text-sm">build</i>&nbsp;&nbsp;Quản lý bảo trì
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <?php if (empty($maintenance_schedules)): ?>
                        <div class="text-center py-4">
                            <i class="material-icons" style="font-size: 48px; color: #ccc;">build_circle</i>
                            <p class="text-secondary mt-2">Chưa có lịch bảo trì nào</p>
                            <?php if ($can_manage_maintenance): ?>
                            <a href="<?= site_url('leader/machine/maintenance/' . $machine->id); ?>" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Tạo lịch bảo trì
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tiêu đề</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($maintenance_schedules, 0, 5) as $maintenance): ?>
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0"><?= $maintenance->title ?></p>
                                            <p class="text-xs text-secondary mb-0"><?= $maintenance->maintenance_type ?></p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0"><?= date('d/m/Y', strtotime($maintenance->start_time)) ?></p>
                                            <p class="text-xs text-secondary mb-0"><?= date('H:i', strtotime($maintenance->start_time)) . ' - ' . date('H:i', strtotime($maintenance->end_time)) ?></p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?php
                                            $maintenance_status_classes = [
                                                'planned' => 'bg-gradient-info',
                                                'in_progress' => 'bg-gradient-warning',
                                                'completed' => 'bg-gradient-success',
                                                'cancelled' => 'bg-gradient-secondary'
                                            ];
                                            $maintenance_status_labels = [
                                                'planned' => 'Đã lên lịch',
                                                'in_progress' => 'Đang thực hiện',
                                                'completed' => 'Hoàn thành',
                                                'cancelled' => 'Đã hủy'
                                            ];
                                            ?>
                                            <span class="badge badge-sm <?= $maintenance_status_classes[$maintenance->status] ?>">
                                                <?= $maintenance_status_labels[$maintenance->status] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (count($maintenance_schedules) > 5): ?>
                        <div class="text-center mt-3">
                            <a href="<?= site_url('leader/machine/maintenance/' . $machine->id); ?>" class="btn btn-outline-primary btn-sm">
                                Xem tất cả (<?= count($maintenance_schedules) ?> lịch)
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
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
