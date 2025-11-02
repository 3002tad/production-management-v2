<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./assets/img/favicon.png">
  <title>
    Production System - Ban Giám Đốc
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700,900" />
  <!-- Nucleo Icons -->
  <link href="<?= site_url('asset/backend/assets/css/nucleo-icons.css'); ?>" rel="stylesheet" />
  <link href="<?= site_url('asset/backend/assets/css/nucleo-svg.css'); ?>" rel="stylesheet" />
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />

  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="<?= site_url('asset/backend/assets/css/material-dashboard.css?v=3.0.0'); ?>" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-200">

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- SIDEBAR NAVIGATION - BAN GIÁM ĐỐC                                -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times  cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand ml-3" href="<?= site_url('BOD/index'); ?>">
        <span class="font-weight-bold text-white">BOD - BAN GIÁM ĐỐC</span>
      </a>
    </div>

    <hr class="horizontal light mt-0 mb-2">
    <div class="col-14">
      <ul class="navbar-nav">
        <!-- Dashboard -->
        <li class="nav-item">
          <a class="nav-link text-white<?= ($navlink === 'beranda') ? ' active bg-gradient-info' : ''; ?>" href="<?= site_url('BOD/index'); ?>">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>

        <!-- Quản lý Đơn hàng -->
        <li class="nav-item navbar-expand-xs">
          <a class="nav-link text-white<?= ($navlink === 'project') ? ' active bg-gradient-info' : ''; ?>" href="<?= site_url('BOD/project'); ?>">
            <div class="text-white me-2 d-flex align-items justify-content">
              <i class="material-icons opacity-10">task</i>
            </div>
            <span class="nav-link-text ms-1">Đơn hàng</span>
          </a>
        </li>

        <!-- Quản lý Khách hàng -->
        <li class="nav-item navbar-expand-xs">
          <a class="nav-link text-white<?= ($navlink === 'customer') ? ' active bg-gradient-info' : ''; ?>" href="<?= site_url('BOD/customer'); ?>">
            <div class="text-white text me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">people</i>
            </div>
            <span class="nav-link-text ms-1">Khách hàng</span>
          </a>
        </li>

        <!-- Quản lý Sản phẩm -->
        <li class="nav-item navbar-expand-xs">
          <a class="nav-link text-white<?= ($navlink === 'product') ? ' active bg-gradient-info' : ''; ?>" href="<?= site_url('BOD/product'); ?>">
            <div class="text-white me-2 d-flex align-items justify-content">
              <i class="material-icons opacity-10">lan</i>
            </div>
            <span class="nav-link-text ms-1">Sản phẩm</span>
          </a>
        </li>

        <!-- Kế hoạch sản xuất (View Only) -->
        <li class="navbar-vertical">
          <div class="text-white text-xs d-flex align-items-center justify-content-left pl-4 pt-2">
            <span class="nav-link-text ms-1 p-2">Giám sát sản xuất</span>
          </div>
        </li>
        <hr class="horizontal light mt-0 mb-2">

        <li class="nav-item navbar-expand-xs">
          <a class="nav-link text-white<?= ($navlink === 'planning') ? ' active bg-gradient-info' : ''; ?>" href="<?= site_url('BOD/planning'); ?>">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">schedule</i>
            </div>
            <span class="nav-link-text ms-1">Kế hoạch sản xuất</span>
          </a>
        </li>

        <!-- Báo cáo -->
        <li class="navbar-vertical">
          <div class="text-white text-xs d-flex align-items-center justify-content-left pl-4 pt-2">
            <span class="nav-link-text ms-1 p-2">Báo cáo</span>
          </div>
        </li>
        <hr class="horizontal light mt-0 mb-2">

        <li class="nav-item navbar-expand-xs">
          <a class="nav-link text-white<?= ($navlink === 'report') ? ' active bg-gradient-info' : ''; ?>" href="<?= site_url('BOD/report'); ?>">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">receipt</i>
            </div>
            <span class="nav-link-text ms-1">Báo cáo tổng hợp</span>
          </a>
        </li>

        <!-- Đăng xuất -->
        <li class="navbar-vertical">
          <div class="text-white text-xs d-flex align-items-center justify-content-left pl-4 pt-2">
            <span class="nav-link-text ms-1 p-2">Hệ thống</span>
          </div>
        </li>
        <hr class="horizontal light mt-0 mb-2">

        <li class="nav-item navbar-expand-xs">
          <a class="nav-link text-white" href="<?= site_url('login/logout'); ?>">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">logout</i>
            </div>
            <span class="nav-link-text ms-1">Đăng xuất</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- MAIN CONTENT                                                     -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <h6 class="font-weight-bolder mb-0">Ban Giám Đốc - Production Management System</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <!-- Search bar (optional) -->
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
            <li class="nav-item px-3 d-flex align-items-center">
              <a href="<?= site_url('BOD/index'); ?>" class="nav-link text-body p-0">
                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->

    <div class="container-fluid py-4">
      <!-- DYNAMIC CONTENT LOADED HERE -->
      <?php $this->load->view($content); ?>

      <!-- Footer -->
      <footer class="footer py-4">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                © <?= date('Y'); ?>, Production Management System v2
                <a href="#" class="font-weight-bold" target="_blank">Ban Giám Đốc Module</a>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- SCRIPTS                                                          -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
  <script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
  <script src="<?= site_url('asset/backend/assets/js/plugins/perfect-scrollbar.min.js'); ?>"></script>
  <script src="<?= site_url('asset/backend/assets/js/plugins/smooth-scrollbar.min.js'); ?>"></script>
  <script src="<?= site_url('asset/backend/assets/js/plugins/chartjs.min.js'); ?>"></script>

  <!-- DataTables -->
  <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

  <script>
    $(document).ready(function() {
      $('#table').DataTable();
    });

    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard -->
  <script src="<?= site_url('asset/backend/assets/js/material-dashboard.min.js?v=3.0.0'); ?>"></script>
</body>

</html>
