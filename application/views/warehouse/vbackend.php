<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./assets/img/favicon.png">
  <title>
    <?php echo $this->lang->line('warehouse_title'); ?>
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
  <style>
    /* Compact table styling for warehouse pages to avoid overflowing cells */
    .warehouse-table {
      table-layout: fixed; /* enforce column widths for ellipsis */
      width: 100%;
    }
    .warehouse-table th, .warehouse-table td {
      padding: .45rem .6rem !important;
      vertical-align: middle !important;
      font-size: 0.95rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    /* wider cell for material name */
    .cell-material { max-width: 260px; }
    .cell-supplier { max-width: 180px; }
    .cell-type, .cell-unit, .cell-date { max-width: 110px; }
    .cell-smallnum { max-width: 100px; text-align: right; }
    /* show full value on hover via title attribute (kept by server-render) */
    .warehouse-table td[title]:hover { cursor: help; }
    @media (max-width: 992px) {
      .warehouse-table th, .warehouse-table td { font-size: 0.88rem; }
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-200">

  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
      <div class="sidenav-header">
      <i class="fas fa-times cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand ml-3" href="<?= site_url('warehouse'); ?>">
        <span class="ms-2 font-weight-bold text-white" style="font-family: Arial, sans-serif;">QUẢN LÝ KHO</span>
      </a>
    </div>    <hr class="horizontal light mt-0 mb-2">
    <div class="col-14">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-white <?= ($navlink === 'beranda') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('warehouse'); ?>">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1"><?php echo $this->lang->line('material_management'); ?></span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white <?= ($navlink === 'material_entry') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('warehouse/material_entry'); ?>">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">inventory</i>
            </div>
            <span class="nav-link-text ms-1"><?php echo $this->lang->line('material_entry'); ?></span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white <?= ($navlink === 'report') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('warehouse/report'); ?>">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">assessment</i>
            </div>
            <span class="nav-link-text ms-1"><?php echo $this->lang->line('material_report'); ?></span>
          </a>
        </li>

      </ul>
    </div>
  </aside>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('warehouse'); ?>"><?php echo $this->lang->line('warehouse_title'); ?></a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
              <?php 
                switch($navlink) {
                  case 'beranda':
                    echo $this->lang->line('dashboard_title');
                    break;
                  case 'material_entry':
                    echo $this->lang->line('material_entry');
                    break;
                  case 'report':
                    echo $this->lang->line('material_report');
                    break;
                  default:
                    echo ucfirst($navlink);
                }
              ?>
            </li>
          </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
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
              <a href="<?= site_url('Login/logout'); ?>" class="btn btn-danger btn-sm">
                <i class="fa fa-sign-out me-1"></i> Đăng xuất
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->

    <div class="container-fluid py-4">
      <?php $this->load->view($content); ?>

      <footer class="footer py-4">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>
                  document.write(new Date().getFullYear())
                </script> Production Management System
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>

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
  <!-- Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="<?= site_url('asset/backend/assets/js/plugins/bootstrap-selectpicker.js'); ?>"></script>
  <!--  DataTables.net Plugin  -->
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>
  <!-- Plugin for Tags -->
  <script src="<?= site_url('asset/backend/assets/js/plugins/bootstrap-tagsinput.js'); ?>"></script>
  <!-- Plugin for Fileupload -->
  <script src="<?= site_url('asset/backend/assets/js/plugins/jasny-bootstrap.min.js'); ?>"></script>
  <!--  Plugin for the Sliders  -->
  <script src="<?= site_url('asset/backend/assets/js/plugins/nouislider.min.js'); ?>"></script>
  <!-- Library for adding dinamically elements -->
  <script src="<?= site_url('asset/backend/assets/js/plugins/arrive.min.js'); ?>"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="<?= site_url('asset/backend/assets/js/material-dashboard.js?v=2.1.2'); ?>" type="text/javascript"></script>
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <script src="<?= site_url('asset/backend/assets/demo/demo.js'); ?>"></script>
  <script src="<?= site_url('asset/backend/assets/js/script.js'); ?>"></script>

  <script>
    $(document).ready(function() {
      $('#example1').DataTable();
    });

    var ctx = document.getElementById("chart-bars").getContext("2d");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["M", "T", "W", "T", "F", "S", "S"],
        datasets: [{
          label: "Sales",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "rgba(255, 255, 255, .8)",
          data: [50, 20, 10, 22, 50, 10, 40],
          maxBarThickness: 6
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              suggestedMin: 0,
              suggestedMax: 500,
              beginAtZero: true,
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
              color: "#fff"
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });

    var ctx2 = document.getElementById("chart-line").getContext("2d");

    new Chart(ctx2, {
      type: "line",
      data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
          label: "Mobile apps",
          tension: 0,
          borderWidth: 0,
          pointRadius: 5,
          pointBackgroundColor: "rgba(255, 255, 255, .8)",
          pointBorderColor: "transparent",
          borderColor: "rgba(255, 255, 255, .8)",
          borderColor: "rgba(255, 255, 255, .8)",
          borderWidth: 4,
          backgroundColor: "transparent",
          fill: true,
          data: [50, 40, 300, 320, 500, 350, 200, 230, 500],
          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });


    var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

    new Chart(ctx3, {
      type: "line",
      data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
          label: "Mobile apps",
          tension: 0,
          borderWidth: 0,
          pointRadius: 5,
          pointBackgroundColor: "rgba(255, 255, 255, .8)",
          pointBorderColor: "transparent",
          borderColor: "rgba(255, 255, 255, .8)",
          borderWidth: 4,
          backgroundColor: "transparent",
          fill: true,
          data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#f8f9fa',
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
  </script>
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
  <script src="./assets/js/material-dashboard.min.js?v=3.0.0"></script>
</body>

</html>