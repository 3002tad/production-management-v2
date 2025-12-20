<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./assets/img/favicon.png">
  <title>
    Production System - Technical Staff
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
        <span class="font-weight-bold text-white">TECHNICAL STAFF</span>
      </a>
    </div>

        <hr class="horizontal light mt-0 mb-2">
            <div class="col-14">
                <ul class="navbar-nav">
                    <!-- Dashboard / Incident List -->
                    <li class="nav-item">
                      <a class="nav-link text-white<?= ($navlink === 'beranda') ? ' active bg-gradient-info' : ''; ?>" href="<?= site_url('uc15_bcsc/technical'); ?>">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                        </div>
                        <span class="nav-link-text ms-1">Danh sách sự cố</span>
                      </a>
                    </li>

                    <!-- Quick: New Incident (leader only) -->
                    <?php if (isset($user_role) && in_array($user_role, ['leader', 'line_manager', 'leader_staff'], true)): ?>
                      <li class="nav-item mt-2">
                        <a class="nav-link text-white d-flex align-items-center justify-content-between" href="<?= site_url('uc16_gn_dp?filter=new'); ?>">
                          <div class="d-flex align-items-center">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                              <i class="material-icons opacity-10">add_alert</i>
                            </div>
                            <span class="nav-link-text ms-1">Sự cố mới</span>
                          </div>
                          <?php if (isset($new_incident_count) && $new_incident_count > 0): ?>
                            <span class="badge bg-danger ms-2" style="font-size:12px;"><?= $new_incident_count; ?></span>
                          <?php endif; ?>
                        </a>
                      </li>
                    <?php endif; ?>

                    <!-- Section: Reports -->
                    <li class="navbar-vertical">
                    <div class="text-white text-xs d-flex align-items-center justify-content-left pl-4 pt-2">
                    <span class="nav-link-text ms-1 p-2">Báo cáo</span>
                    </div>
                    </li>
                    <hr class="horizontal light mt-0 mb-2">
                    
                    <!-- Incident Reports -->
                    <li class="nav-item navbar-expand-xs">
                      <a class="nav-link text-white<?= ($navlink === 'incident') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('uc15_bcsc/technical'); ?>">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">report_problem</i>
                        </div>
                        <span class="nav-link-text ms-1">Sự cố</span>
                      </a>
                    </li>

                    <!-- Logout -->
                    <hr class="horizontal light mt-4 mb-2">
                    <li class="nav-item navbar-expand-xs">
                      <a class="nav-link text-white" href="<?= site_url('login/logout'); ?>">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">exit_to_app</i>
                        </div>
                        <span class="nav-link-text ms-1">Đăng xuất</span>
                      </a>
                    </li>
                </ul>
            </div>
    </aside>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Main Data -->
        <?php $this->load->view($content); ?>
        <!-- End of Main Data -->
    </main>

    <footer class="footer">
        <div class="container-fluid">
            <div class="copyright float-right pt-5 text-sm">
                © Production Management System - Technical Staff
            </div>
        </div>
    </footer>
  <!--   Core JS Files   -->
      

    <script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
    <script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
    <script src="<?= site_url('asset/backend/assets/js/plugins/perfect-scrollbar.min.js'); ?>"></script>
    <script src="<?= site_url('asset/backend/assets/js/plugins/smooth-scrollbar.min.js'); ?>"></script>
    <script src="<?= site_url('asset/backend/assets/js/plugins/chartjs.min.js'); ?>"></script>
    <!-- Forms Validations Plugin -->
    <script src="<?= site_url('asset/backend/assets/js/plugins/jquery.validate.min.js'); ?>"></script>
    <!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
    <script src="<?= site_url('asset/backend/assets/js/plugins/jquery.bootstrap-wizard.js'); ?>"></script>
    <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
    <script src="<?= site_url('asset/backend/assets/js/plugins/bootstrap-selectpicker.js'); ?>"></script>
    <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  --><script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>
    <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
    <script src="<?= site_url('asset/backend/assets/js/plugins/bootstrap-tagsinput.js'); ?>"></script>
    <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
    <script src="<?= site_url('asset/backend/assets/js/plugins/jasny-bootstrap.min.js'); ?>"></script>
    <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
    <script src="<?= site_url('asset/backend/assets/js/plugins/nouislider.min.js'); ?>"></script>
    <!-- Library for adding dinamically elements -->
    <script src="<?= site_url('asset/backend/assets/js/plugins/arrive.min.js'); ?>"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="<?= site_url('asset/backend/assets/js/material-dashboard.js?v=2.1.2'); ?>" type="text/javascript"></script>
    <!-- Material Dashboard DEMO methods, don't include it in your project! -->
    <script src="<?= site_url('asset/backend/assets/demo/demo.js'); ?>"></script>
    <script src="<?= site_url('asset/backend/assets/js/script.js'); ?>"></script>

  <script>
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
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  
</body>

</html>
