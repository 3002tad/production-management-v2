<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./assets/img/favicon.png">
  <title>
    Production System 
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

  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times  cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand ml-3" href="index">
        <span class="font-weight-bold text-white">PRODUCTION SYSTEM</span>
      </a>
    </div>

        <hr class="horizontal light mt-0 mb-2">
            <div class="col-14">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-white<?= ($navlink === 'beranda') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('uc15_qlns/uc15_bcsc'); ?>">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">dashboard</i>
                            </div>
                            <span class="nav-link-text ms-1">Báo cáo sự cố</span>
                        </a>
                    </li>

                    <li class="navbar-vertical">
                    <div class="text-white text-xs d-flex align-items-center justify-content-left pl-4 pt-2">
                    <span class="nav-link-text ms-1 p-2">HÀNH ĐỘNG</span>
                    </div>
                    </li>
                    <hr class="horizontal light mt-0 mb-2">

                    <li class="nav-item navbar-expand-xs">
                        <a class="nav-link text-white" href="<?= site_url('uc15_qlns/uc15_bcsc/add'); ?>">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">add_circle</i>
                            </div>
                            <span class="nav-link-text ms-1">Thêm báo cáo</span>
                        </a>
                    </li>

                    <li class="nav-item navbar-expand-xs">
                        <a class="nav-link text-white" href="<?= site_url('uc15_qlns/uc15_bcsc'); ?>">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">list</i>
                            </div>
                            <span class="nav-link-text ms-1">Danh sách báo cáo</span>
                        </a>
                    </li>

                    <li class="nav-item navbar-expand-xs">
                        <a class="nav-link text-white" href="<?= site_url('uc15_qlns/uc15_bcsc'); ?>">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">assessment</i>
                            </div>
                            <span class="nav-link-text ms-1">Thống kê sự cố</span>
                        </a>
                    </li>
                </ul>
            </div>
        <div class="sidenav-footer mx-3 ">
            <div class="card card-plain shadow-none rounded-lg mb-4 pb-2">
            <img src="<?= site_url('asset/backend/assets/img/logos/argon-white.png'); ?>" class="navbar-brand" alt="...">
            </div>
            <a href="<?= site_url('login/logout'); ?>" class="btn btn-dark btn-sm mb-0 w-100">Đăng xuất</a>
        </div>
  </aside>

  <main class="main-content position-relative max-height-vh-100 h-100">

    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Trang</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Báo cáo sự cố</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Quản lý báo cáo sự cố</h6>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <h6 class="text-sm font-weight-bolder mb-0">Hệ thống quản lý sản xuất</h6>
                </div>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <div class="container-fluid py-4">
      <?= $this->load->view($content, NULL, TRUE); ?>
    </div>
  </main>

  <!--   Core JS Files   -->
  <script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
  <script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
  <script src="<?= site_url('asset/backend/assets/js/plugins/perfect-scrollbar.min.js'); ?>"></script>
  <script src="<?= site_url('asset/backend/assets/js/plugins/smooth-scrollbar.min.js'); ?>"></script>
  <script src="<?= site_url('asset/backend/assets/js/plugins/chartjs.min.js'); ?>"></script>
  <script src="<?= site_url('asset/backend/assets/js/material-dashboard.min.js?v=3.0.0'); ?>"></script>
</body>

</html>
