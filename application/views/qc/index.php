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
    
    <style>
        .badge-pending-qc {
            background: linear-gradient(195deg, #FFA726 0%, #FB8C00 100%);
            color: white;
        }
        
        .badge-verified {
            background: linear-gradient(195deg, #66BB6A 0%, #43A047 100%);
            color: white;
        }
        
        .badge-rejected {
            background: linear-gradient(195deg, #EF5350 0%, #E53935 100%);
            color: white;
        }
        
        .btn-inspect {
            background: linear-gradient(195deg, #1A73E8 0%, #1662C4 100%);
            color: white;
            border: none;
        }
        
        .btn-inspect:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.12);
            color: white;
        }
    </style>
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
                <a class="nav-link text-white active bg-gradient-primary" href="<?= site_url('qc/'); ?>">
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
                <a class="nav-link text-white" href="<?= site_url('qc/reports'); ?>">
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
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Danh sách Pending-QC</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Kiểm tra & xác minh chất lượng</h6>
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
        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="material-icons">check_circle</i></span>
            <span class="alert-text"><strong>Thành công!</strong> <?= $this->session->flashdata('success') ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="material-icons">info</i></span>
            <span class="alert-text"><strong>Thông tin:</strong> <?= $this->session->flashdata('info') ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="material-icons">error</i></span>
            <span class="alert-text"><strong>Lỗi!</strong> <?= $this->session->flashdata('error') ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Filter Panel (Use Case - Bước 2: Lọc theo ca/line/dự án) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Bộ lọc phiếu chốt ca</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?= site_url('qc/'); ?>" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Line sản xuất</label>
                                <input type="text" class="form-control" name="line_code" 
                                       value="<?= $filters['line_code'] ?? '' ?>" 
                                       placeholder="VD: LINE-01">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ca làm việc</label>
                                <input type="text" class="form-control" name="shift_code" 
                                       value="<?= $filters['shift_code'] ?? '' ?>" 
                                       placeholder="VD: CA1, CA2">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mã dự án</label>
                                <input type="text" class="form-control" name="project_code" 
                                       value="<?= $filters['project_code'] ?? '' ?>" 
                                       placeholder="VD: PRJ001">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Từ ngày</label>
                                <input type="date" class="form-control" name="date_from" 
                                       value="<?= $filters['date_from'] ?? '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Đến ngày</label>
                                <input type="date" class="form-control" name="date_to" 
                                       value="<?= $filters['date_to'] ?? '' ?>">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="material-icons">search</i> Lọc
                                </button>
                                <a href="<?= site_url('qc/'); ?>" class="btn btn-outline-secondary">
                                    <i class="material-icons">refresh</i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Closures Table (Use Case - Bước 1: Danh sách Pending-QC) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6>Danh sách phiếu chốt ca chờ kiểm định</h6>
                                <p class="text-sm mb-0">
                                    <span class="font-weight-bold">Tổng số: <?= count($closures) ?> phiếu</span> chờ xác minh chất lượng
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã phiếu</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Line / Ca</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dự án / Sản phẩm</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Số lượng TP</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Số lượng PP</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian chốt</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($closures)): ?>
                                        <?php foreach ($closures as $closure): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm"><?= $closure->code ?></h6>
                                                    <p class="text-xs text-secondary mb-0">Lot: <?= $closure->lot_code ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0"><?= $closure->line_code ?></p>
                                            <p class="text-xs text-secondary mb-0">Ca: <?= $closure->shift_code ?></p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0"><?= $closure->project_name ?? $closure->project_code ?></p>
                                            <p class="text-xs text-secondary mb-0"><?= $closure->product_name ?? $closure->product_code ?></p>
                                            <?php if ($closure->variant): ?>
                                            <p class="text-xs text-info mb-0"><i class="material-icons text-xs">style</i> <?= $closure->variant ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold"><?= number_format($closure->qty_finished) ?></span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold"><?= number_format($closure->qty_waste) ?></span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold"><?= date('d/m/Y H:i', strtotime($closure->closed_at)) ?></span>
                                            <p class="text-xs text-secondary mb-0">Bởi: <?= $closure->closed_by ?></p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm badge-pending-qc">PENDING QC</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <!-- Use Case - Bước 3: Vào bản ghi để kiểm tra -->
                                            <form method="POST" action="<?= site_url('qc/createSession'); ?>" style="display: inline;">
                                                <input type="hidden" name="closure_id" value="<?= $closure->id ?>">
                                                <button type="submit" class="btn btn-inspect btn-sm mb-0">
                                                    <i class="material-icons text-sm">fact_check</i> Kiểm tra
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">inbox</i>
                                                <p class="text-secondary mb-0 mt-2">Không có phiếu chốt ca nào chờ kiểm định</p>
                                                <p class="text-xs text-secondary">Tất cả các phiếu đã được xác minh hoặc chưa có phiếu mới</p>
                                            </div>
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

        <!-- Statistics Summary -->
        <div class="row mt-4">
            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10">pending_actions</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Chờ kiểm định</p>
                            <h4 class="mb-0"><?= count($closures) ?></h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0"><span class="text-warning text-sm font-weight-bolder">Pending-QC</span> phiếu chốt ca</p>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10">verified</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Đã duyệt hôm nay</p>
                            <h4 class="mb-0"><?= $stats['verified'] ?? 0 ?></h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0"><span class="text-success text-sm font-weight-bolder">Verified</span> lô hàng</p>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10">cancel</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Đã từ chối hôm nay</p>
                            <h4 class="mb-0"><?= $stats['rejected'] ?? 0 ?></h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0"><span class="text-danger text-sm font-weight-bolder">Rejected</span> lô hàng</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer py-4 mt-4">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-lg-between">
                    <div class="col-lg-6 mb-lg-0 mb-4">
                        <div class="copyright text-center text-sm text-muted text-lg-start">
                            © QC Module - Production Management System
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</main>

<!-- Core JS Files -->
<script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/plugins/perfect-scrollbar.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/plugins/smooth-scrollbar.min.js'); ?>"></script>
<script src="<?= site_url('asset/backend/assets/js/material-dashboard.min.js?v=3.0.0'); ?>"></script>

<script>
// Auto-hide flash messages after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Confirm before inspect
document.querySelectorAll('form[action*="createSession"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Bắt đầu phiên kiểm tra QC cho phiếu chốt ca này?')) {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>
