<style>
    .zone-card {
        transition: all 0.3s ease;
        border-left: 4px solid #2196F3;
    }
    .zone-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important;
    }
</style>

<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/zone/'); ?>">Khu vực</a></li>
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

    <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="material-icons">error</i></span>
        <span class="alert-text"><?= $this->session->flashdata('error') ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">domain</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Tổng Số Khu</p>
                        <h4 class="mb-0"><?= count($zones) ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">settings_input_component</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Dây Chuyền</p>
                        <h4 class="mb-0"><?= array_sum(array_column($zones, 'line_count')) ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">precision_manufacturing</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Máy Móc</p>
                        <h4 class="mb-0"><?= array_sum(array_column($zones, 'machine_count')) ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Panel -->
    <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6>Danh sách Khu vực</h6>
            <?php if ($can_edit): ?>
            <a href="<?= site_url('leader/zone/create'); ?>" class="btn btn-primary btn-sm mb-0">
                <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Thêm Khu Mới
            </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= site_url('leader/zone/'); ?>">
                <div class="row">
                    <div class="col-md-10">
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label">Tìm kiếm theo mã hoặc tên khu...</label>
                            <input type="text" class="form-control" name="search" value="<?= $filters['search'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary mb-0 w-100">
                            <i class="material-icons">search</i> Tìm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Zones List -->
    <div class="row">
        <?php if (empty($zones)): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="material-icons" style="font-size: 64px; color: #ccc;">domain</i>
                    <p class="text-secondary mt-3">Chưa có khu vực nào</p>
                    <?php if ($can_edit): ?>
                    <a href="<?= site_url('leader/zone/create'); ?>" class="btn btn-primary mt-2">
                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Tạo Khu Đầu Tiên
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($zones as $zone): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card zone-card h-100">
                    <div class="card-header pb-0" style="background: linear-gradient(195deg, #2196F315 0%, #2196F305 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1">
                                    <i class="material-icons" style="vertical-align: middle; color: #2196F3;">domain</i>
                                    <?= $zone->zone_name ?>
                                </h5>
                                <p class="text-xs text-secondary mb-0">Mã: <?= $zone->zone_code ?></p>
                            </div>
                            <span class="badge <?= $zone->status == 1 ? 'bg-gradient-success' : 'bg-gradient-secondary' ?>">
                                <?= $zone->status == 1 ? 'Hoạt động' : 'Ngừng hoạt động' ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($zone->description)): ?>
                        <p class="text-sm text-secondary mb-3">
                            <i class="material-icons text-xs">info</i> <?= $zone->description ?>
                        </p>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <p class="text-xs text-secondary mb-0">Tầng</p>
                                <p class="text-sm font-weight-bold mb-0"><?= $zone->floor ?: 'N/A' ?></p>
                            </div>
                            <div class="col-6 mb-3">
                                <p class="text-xs text-secondary mb-0">Tòa nhà</p>
                                <p class="text-sm font-weight-bold mb-0"><?= $zone->building ?: 'N/A' ?></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-xs shadow text-center me-2" style="background: #4CAF50;">
                                        <i class="material-icons opacity-10" style="color: white; font-size: 14px;">settings_input_component</i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-secondary mb-0">Dây chuyền</p>
                                        <p class="text-sm font-weight-bold mb-0"><?= $zone->line_count ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-xs shadow text-center me-2" style="background: #FF9800;">
                                        <i class="material-icons opacity-10" style="color: white; font-size: 14px;">precision_manufacturing</i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-secondary mb-0">Máy móc</p>
                                        <p class="text-sm font-weight-bold mb-0"><?= $zone->machine_count ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer pt-0">
                        <div class="d-flex justify-content-end">
                            <a href="<?= site_url('leader/zone/detail/' . $zone->zone_id); ?>" class="btn btn-link text-primary text-gradient px-2 mb-0">
                                <i class="material-icons text-sm">visibility</i> CHI TIẾT
                            </a>
                            <?php if ($can_edit): ?>
                            <a href="<?= site_url('leader/zone/edit/' . $zone->zone_id); ?>" class="btn btn-link text-dark px-2 mb-0">
                                <i class="material-icons text-sm">edit</i> SỬA
                            </a>
                            <button onclick="deleteZone(<?= $zone->zone_id ?>, '<?= $zone->zone_name ?>')" class="btn btn-link text-danger px-2 mb-0">
                                <i class="material-icons text-sm">delete</i> XÓA
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteZone(zoneId, zoneName) {
    if (confirm('Bạn có chắc chắn muốn xóa khu vực "' + zoneName + '"?\n\nLưu ý: Chỉ có thể xóa khu vực không có dây chuyền.')) {
        fetch('<?= site_url('leader/zone/delete/') ?>' + zoneId, {
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

// Auto-hide alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        const closeBtn = alert.querySelector('.btn-close');
        if (closeBtn) closeBtn.click();
    });
}, 5000);
</script>
