<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Hệ thống QC</title>
    
    <!-- Fonts -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700,900" />
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    
    <!-- Material Dashboard CSS -->
    <link href="<?= site_url('asset/backend/assets/css/material-dashboard.css?v=3.0.0'); ?>" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-200">

<!-- Sidebar -->
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="<?= site_url('qc/'); ?>">
            <span class="ms-1 font-weight-bold text-white">PRODUCTION SYSTEM</span>
        </a>
    </div>
    
    <hr class="horizontal light mt-0 mb-2">
    
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">QC - KIỂM SOÁT CHẤT LƯỢNG</h6>
            </li>
            
            <!-- Pending Inspections -->
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= site_url('qc/'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">pending_actions</i>
                    </div>
                    <span class="nav-link-text ms-1">Phiếu chốt ca chờ QC</span>
                </a>
            </li>
            
            <!-- My Sessions -->
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= site_url('qc/sessions'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">assignment</i>
                    </div>
                    <span class="nav-link-text ms-1">Phiên kiểm tra của tôi</span>
                </a>
            </li>
            
            <!-- Adjustment Requests -->
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= site_url('qc/adjustments'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">build_circle</i>
                    </div>
                    <span class="nav-link-text ms-1">Yêu cầu điều chỉnh</span>
                </a>
            </li>
            
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">BÁO CÁO</h6>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white active bg-gradient-primary" href="<?= site_url('qc/reports'); ?>">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">analytics</i>
                    </div>
                    <span class="nav-link-text ms-1">Báo cáo QC</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Main Content -->
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">QC</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Báo cáo</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Báo cáo kiểm soát chất lượng</h6>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    <!-- User info -->
                </div>
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a href="<?= site_url('login/logout'); ?>" class="nav-link text-body font-weight-bold px-0">
                            <i class="fa fa-user me-sm-1"></i>
                            <span class="d-sm-inline d-none"><?= $user['full_name'] ?? 'QC Inspector' ?></span>
                            <i class="material-icons ms-2">logout</i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->
    
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
</main>

<!-- Scripts -->
<script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/material-dashboard.min.js?v=3.0.0'); ?>"></script>
</body>
</html>
