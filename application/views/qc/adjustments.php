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
                <a class="nav-link text-white active bg-gradient-primary" href="<?= site_url('qc/adjustments'); ?>">
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
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Yêu cầu điều chỉnh</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Quản lý yêu cầu điều chỉnh chất lượng</h6>
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

        <!-- Adjustment Requests Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Danh sách yêu cầu điều chỉnh</h6>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="<?= site_url('qc/'); ?>" class="btn btn-sm btn-primary">
                                    <i class="material-icons text-sm">arrow_back</i>
                                    Quay lại
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã yêu cầu</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Mã chốt ca</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Line sản xuất</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Lý do</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Trạng thái</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($requests)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons" style="font-size: 3rem; opacity: 0.3;">info</i>
                                                    <p class="mt-2">Không có yêu cầu điều chỉnh nào.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($requests as $req): ?>
                                            <tr>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><code><?= $req->code ?></code></p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><?= $req->closure_code ?></p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><?= $req->line_code ?></p>
                                                </td>
                                                <td style="max-width: 300px; word-wrap: break-word; white-space: normal;">
                                                    <p class="text-xs text-secondary mb-0">
                                                        <?= htmlspecialchars($req->reason) ?>
                                                    </p>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $statusBadge = 'badge-pending-qc';
                                                        $statusText = 'OPEN';
                                                        
                                                        if ($req->status === 'ACKED') {
                                                            $statusBadge = 'badge-warning';
                                                            $statusText = 'ĐÃ XÁC NHẬN';
                                                        } elseif ($req->status === 'CLOSED' || $req->status === 'RESOLVED') {
                                                            $statusBadge = 'badge-verified';
                                                            $statusText = 'ĐÃ GIẢI QUYẾT';
                                                        } elseif ($req->status === 'OPEN') {
                                                            $statusText = 'MỞ';
                                                        }
                                                    ?>
                                                    <span class="badge <?= $statusBadge ?>"><?= $statusText ?></span>
                                                </td>
                                                <td>
                                                    <p class="text-xs text-secondary mb-0">
                                                        <?= date('d/m/Y H:i', strtotime($req->created_at)) ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
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
