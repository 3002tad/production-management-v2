<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - QC Module</title>
    <link rel="stylesheet" href="<?= base_url('asset/Backend/assets/css/material-dashboard.min.css') ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body class="g-sidenav-show bg-gray-200">
    
    <div class="container-fluid py-4">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng số</p>
                                    <h5 class="font-weight-bolder mb-0"><?= $stats['total'] ?></h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="material-icons opacity-10">assignment</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Phê duyệt</p>
                                    <h5 class="font-weight-bolder mb-0 text-success"><?= $stats['approved'] ?></h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="material-icons opacity-10">check_circle</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Từ chối</p>
                                    <h5 class="font-weight-bolder mb-0 text-danger"><?= $stats['rejected'] ?></h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                    <i class="material-icons opacity-10">cancel</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Tỷ lệ lỗi TB</p>
                                    <h5 class="font-weight-bolder mb-0"><?= number_format($stats['avg_defect_rate'], 2) ?>%</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="material-icons opacity-10">analytics</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="<?= site_url('qc/reports') ?>" class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Line</label>
                                <input type="text" class="form-control" name="line_code" value="<?= $filters['line_code'] ?? '' ?>" placeholder="LINE-01">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Ca làm việc</label>
                                <input type="text" class="form-control" name="shift_code" value="<?= $filters['shift_code'] ?? '' ?>" placeholder="CA1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Dự án</label>
                                <input type="text" class="form-control" name="project_code" value="<?= $filters['project_code'] ?? '' ?>" placeholder="PRJ001">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Kết quả</label>
                                <select class="form-select" name="result">
                                    <option value="">-- Tất cả --</option>
                                    <option value="APPROVE" <?= ($filters['result'] ?? '') == 'APPROVE' ? 'selected' : '' ?>>Phê duyệt</option>
                                    <option value="REJECT" <?= ($filters['result'] ?? '') == 'REJECT' ? 'selected' : '' ?>>Từ chối</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Từ ngày</label>
                                <input type="date" class="form-control" name="date_from" value="<?= $filters['date_from'] ?? '' ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Đến ngày</label>
                                <input type="date" class="form-control" name="date_to" value="<?= $filters['date_to'] ?? '' ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="material-icons">search</i> Lọc
                                </button>
                                <a href="<?= site_url('qc/reports') ?>" class="btn btn-secondary">
                                    <i class="material-icons">refresh</i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Danh sách báo cáo (<?= count($sessions) ?> kết quả)</h6>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Session</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Closure / Line</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dự án / Sản phẩm</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Người kiểm tra</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Kết quả</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Defect Rate</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quyết định bởi</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($sessions)): ?>
                                        <?php foreach ($sessions as $session): ?>
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $session->code ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $session->closure_code ?></p>
                                                <p class="text-xs text-secondary mb-0"><?= $session->line_code ?> - <?= $session->shift_code ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $session->project_name ?></p>
                                                <p class="text-xs text-secondary mb-0"><?= $session->product_name ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs mb-0"><?= $session->inspector_name ?></p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <?php if ($session->result == 'APPROVE'): ?>
                                                    <span class="badge badge-sm bg-gradient-success">✓ APPROVE</span>
                                                <?php else: ?>
                                                    <span class="badge badge-sm bg-gradient-danger">✗ REJECT</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-xs <?= $session->defect_rate > $session->aql ? 'text-danger' : 'text-success' ?> font-weight-bold">
                                                    <?= number_format($session->defect_rate, 2) ?>%
                                                </span>
                                                <span class="text-xxs text-secondary">(AQL: <?= $session->aql ?>%)</span>
                                            </td>
                                            <td>
                                                <p class="text-xs mb-0"><?= $session->decided_by_name ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs mb-0"><?= date('d/m/Y', strtotime($session->decided_at)) ?></p>
                                                <p class="text-xs text-secondary mb-0"><?= date('H:i', strtotime($session->decided_at)) ?></p>
                                            </td>
                                            <td class="align-middle">
                                                <a href="<?= site_url('qc/sessions/' . $session->id) ?>" class="btn btn-sm btn-info">
                                                    <i class="material-icons text-sm">visibility</i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">bar_chart</i>
                                                <p class="text-secondary">Không có dữ liệu báo cáo</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('asset/Backend/assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('asset/Backend/assets/js/core/bootstrap.min.js') ?>"></script>
</body>
</html>
