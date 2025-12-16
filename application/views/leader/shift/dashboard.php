<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Ca làm việc</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Danh sách</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Quản Lý Ca Làm Việc</h6>
        </nav>
    </div>
</nav>

<div class="container-fluid py-4">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">schedule</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Tổng Số Ca</p>
                        <h4 class="mb-0"><?= count($shifts) ?></h4>
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
                        <p class="text-sm mb-0 text-capitalize">Đang Chạy</p>
                        <h4 class="mb-0">
                            <?= count(array_filter($shifts, function($s) { return $s->shift_status == 2; })) ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">people</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Thiếu Nhân Sự</p>
                        <h4 class="mb-0">
                            <?= count(array_filter($shifts, function($s) { return $s->staff_status == 'insufficient'; })) ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">check_circle</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Hoàn Thành</p>
                        <h4 class="mb-0">
                            <?= count(array_filter($shifts, function($s) { return $s->shift_status == 3; })) ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
    <!-- Search & Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Tìm kiếm & Lọc</h6>
                    <a href="<?= site_url('leader/shift/create'); ?>" class="btn btn-primary btn-sm">
                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Tạo Ca Mới
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= site_url('leader/shift'); ?>" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Dây chuyền</label>
                            <select name="line_id" class="form-control">
                                <option value="">Tất cả</option>
                                <?php foreach ($lines as $line): ?>
                                    <option value="<?= $line->id ?>" <?= ($filters['line_id'] == $line->id) ? 'selected' : '' ?>>
                                        <?= $line->line_code ?> - <?= $line->line_name ?>                                        <?php if (!empty($line->zone_name)): ?>
                                            (<?= $line->zone_name ?>)
                                        <?php endif; ?>                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Ngày</label>
                            <input type="date" name="shift_date" class="form-control" value="<?= $filters['shift_date'] ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="date_from" class="form-control" value="<?= $filters['date_from'] ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="date_to" class="form-control" value="<?= $filters['date_to'] ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Trạng thái ca</label>
                            <select name="shift_status" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="1" <?= ($filters['shift_status'] == '1') ? 'selected' : '' ?>>Chưa bắt đầu</option>
                                <option value="2" <?= ($filters['shift_status'] == '2') ? 'selected' : '' ?>>Đang chạy</option>
                                <option value="3" <?= ($filters['shift_status'] == '3') ? 'selected' : '' ?>>Hoàn thành</option>
                                <option value="4" <?= ($filters['shift_status'] == '4') ? 'selected' : '' ?>>Tạm dừng</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="material-icons">search</i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Shifts List as Cards -->
    <div class="row">
        <?php if (empty($shifts)): ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="material-icons text-secondary" style="font-size: 64px;">event_busy</i>
                        <p class="text-sm text-secondary mb-0 mt-3">Không có ca nào trong khoảng thời gian này</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($shifts as $shift): 
                // Determine border color based on status
                $border_color = 'border-secondary';
                if ($shift->shift_status == 2) {
                    $border_color = 'border-primary';
                } elseif ($shift->shift_status == 3) {
                    $border_color = 'border-success';
                } elseif ($shift->shift_status == 4) {
                    $border_color = 'border-warning';
                }
                
                // Status badges
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
                
                $staff_badge_class = 'bg-gradient-secondary';
                $staff_text = 'Chưa phân';
                if ($shift->staff_status == 'sufficient') {
                    $staff_badge_class = 'bg-gradient-success';
                    $staff_text = $shift->assigned_staff_count . ' Người';
                } elseif ($shift->staff_status == 'insufficient') {
                    $staff_badge_class = 'bg-gradient-warning';
                    $staff_text = 'Thiếu NS';
                } elseif ($shift->staff_status == 'conflict') {
                    $staff_badge_class = 'bg-gradient-danger';
                    $staff_text = 'Xung đột';
                }
                
                $machine_badge_class = 'bg-gradient-secondary';
                $machine_text = 'Chưa gán';
                if ($shift->machine_status == 'assigned') {
                    $machine_badge_class = 'bg-gradient-info';
                    $machine_text = $shift->assigned_machine_count . ' Máy';
                } elseif ($shift->machine_status == 'maintenance') {
                    $machine_badge_class = 'bg-gradient-warning';
                    $machine_text = 'Bảo trì';
                } elseif ($shift->machine_status == 'down') {
                    $machine_badge_class = 'bg-gradient-danger';
                    $machine_text = 'Hỏng';
                }
            ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-3 <?= $border_color ?> h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="icon icon-shape bg-gradient-info shadow border-radius-md">
                                    <i class="material-icons opacity-10">schedule</i>
                                </div>
                            </div>
                            <span class="badge badge-sm <?= $status_badge_class ?>"><?= $status_text ?></span>
                        </div>
                        
                        <h6 class="mb-1"><?= $shift->shift_name ?></h6>
                        <p class="text-sm text-muted mb-3"><?= $shift->shift_code ?></p>
                        
                        <div class="row mb-2">
                            <div class="col-6">
                                <p class="text-xs text-muted mb-0"><i class="material-icons text-sm">event</i> Ngày</p>
                                <p class="text-sm font-weight-bold mb-0"><?= date('d/m/Y', strtotime($shift->shift_date)) ?></p>
                            </div>
                            <div class="col-6">
                                <p class="text-xs text-muted mb-0"><i class="material-icons text-sm">access_time</i> Giờ</p>
                                <p class="text-sm font-weight-bold mb-0">
                                    <?= date('H:i', strtotime($shift->start_time)) ?> - <?= date('H:i', strtotime($shift->end_time)) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12 mb-2">
                                <p class="text-xs text-muted mb-0">
                                    <i class="material-icons text-sm">factory</i> Dây chuyền
                                </p>
                                <p class="text-sm font-weight-bold mb-0">
                                    <?= $shift->line_code ?> - <?= $shift->line_name ?>
                                </p>
                            </div>
                            <div class="col-6">
                                <p class="text-xs text-muted mb-0"><i class="material-icons text-sm">bar_chart</i> Sản lượng</p>
                                <p class="text-sm font-weight-bold mb-0"><?= $shift->actual_quantity ?>/<?= $shift->target_quantity ?></p>
                            </div>
                            <div class="col-6">
                                <p class="text-xs text-muted mb-0"><i class="material-icons text-sm">person</i> Tạo bởi</p>
                                <p class="text-sm font-weight-bold mb-0"><?= $shift->created_by_username ?? 'N/A' ?></p>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge badge-sm <?= $staff_badge_class ?>">
                                <i class="material-icons text-xs">people</i> <?= $staff_text ?>
                            </span>
                            <span class="badge badge-sm <?= $machine_badge_class ?>">
                                <i class="material-icons text-xs">precision_manufacturing</i> <?= $machine_text ?>
                            </span>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="<?= site_url('leader/shift/detail/' . $shift->shift_id) ?>" 
                               class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="material-icons text-sm">visibility</i> Chi tiết
                            </a>
                            <a href="<?= site_url('leader/shift/edit/' . $shift->shift_id) ?>" 
                               class="btn btn-sm btn-outline-dark">
                                <i class="material-icons text-sm">edit</i>
                            </a>
                            <button onclick="deleteShift(<?= $shift->shift_id ?>, '<?= $shift->shift_name ?>')" 
                                    class="btn btn-sm btn-outline-danger">
                                <i class="material-icons text-sm">delete</i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.border-left-3 {
    border-left: 3px solid;
}
</style>
<script>
function deleteShift(shiftId, shiftName) {
    if (confirm('Bạn có chắc chắn muốn xóa ca "' + shiftName + '"?\n\nLưu ý: Chỉ có thể xóa ca chưa bắt đầu. Thao tác này sẽ xóa tất cả phân công nhân sự và máy móc.')) {
        fetch('<?= site_url('leader/shift/delete/') ?>' + shiftId, {
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
            alert('Có lỗi xảy ra khi xóa ca');
        });
    }
}
</script>