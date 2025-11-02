<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Panel - Production Management</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('asset/Backend/assets/css/bootstrap.min.css') ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .user-info {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: rgba(255,255,255,0.1);
            margin: 0 10px 20px;
            border-radius: 8px;
        }
        .user-info .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #667eea;
            margin-right: 15px;
        }
        .content-wrapper {
            padding: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
            padding: 15px 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .stat-card h3 {
            font-size: 32px;
            margin-bottom: 5px;
        }
        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <!-- User Info -->
                <div class="user-info text-white">
                    <div class="avatar">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div>
                        <strong><?= $this->session->userdata('full_name') ?: $this->session->userdata('username') ?></strong>
                        <br>
                        <small><?= $this->session->userdata('role_display_name') ?></small>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="nav flex-column">
                    <a class="nav-link <?= ($navlink == 'dashboard') ? 'active' : '' ?>" href="<?= base_url('warehouse/') ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link <?= ($navlink == 'material') ? 'active' : '' ?>" href="<?= base_url('warehouse/material') ?>">
                        <i class="fas fa-cubes"></i> Nguyên vật liệu
                    </a>
                    <a class="nav-link <?= ($navlink == 'stock_in') ? 'active' : '' ?>" href="<?= base_url('warehouse/stock_in') ?>">
                        <i class="fas fa-arrow-down"></i> Nhập kho NVL
                    </a>
                    <a class="nav-link <?= ($navlink == 'stock_out') ? 'active' : '' ?>" href="<?= base_url('warehouse/stock_out') ?>">
                        <i class="fas fa-arrow-up"></i> Xuất kho NVL
                    </a>
                    <a class="nav-link <?= ($navlink == 'finished_product') ? 'active' : '' ?>" href="<?= base_url('warehouse/finished_product') ?>">
                        <i class="fas fa-boxes"></i> Kho thành phẩm
                    </a>
                    <a class="nav-link <?= ($navlink == 'report') ? 'active' : '' ?>" href="<?= base_url('warehouse/report') ?>">
                        <i class="fas fa-chart-bar"></i> Báo cáo tồn kho
                    </a>
                    <hr style="border-color: rgba(255,255,255,0.2)">
                    <a class="nav-link" href="<?= base_url('login/logout') ?>">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light mb-4">
                    <div class="container-fluid">
                        <h4 class="mb-0">
                            <i class="fas fa-warehouse text-primary"></i>
                            Warehouse Management System
                        </h4>
                        <span class="badge badge-primary">
                            <?= date('d/m/Y H:i') ?>
                        </span>
                    </div>
                </nav>

                <!-- Content Area -->
                <div class="content-wrapper">
                    <?php $this->load->view($content); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('asset/Backend/assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('asset/Backend/assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
