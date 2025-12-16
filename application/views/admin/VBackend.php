<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url('asset/Backend/assets/img/apple-icon.png'); ?>">
  <link rel="icon" type="image/png" href="<?php echo base_url('asset/Backend/assets/img/favicon.png'); ?>">  <title>
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
<!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
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
                                        <li class="navbar-vertical">
                    <div class="text-white text-xs d-flex align-items-center justify-content-left pl-4 pt-2">
                    <span class="nav-link-text ms-1 p-2">QUẢN LÝ NGƯỜI DÙNG</span>
                    </div>
                    </li>
                    <hr class="horizontal light mt-0 mb-2">
                                        <li class="nav-item navbar-expand-xs">
                        <a class="nav-link text-white<?= ($navlink === 'staff') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('admin/staff'); ?>">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">manage_accounts</i>
                            </div>
                            <span class="nav-link-text ms-1">Nhân viên</span>
                        </a>
                    </li>
                    <li class="nav-item navbar-expand-xs">
                        <a class="nav-link text-white<?= ($navlink === 'user') ? 'active bg-gradient-info' : ''; ?>" href="<?= site_url('admin/user'); ?>">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">person</i>
                            </div>
                            <span class="nav-link-text ms-1">Người dùng</span>
                        </a>
                    </li>

                    <li class="navbar-vertical">
                    <div class="text-white text-xs d-flex align-items-center justify-content-left pl-4 pt-2">
                    <span class="nav-link-text ms-1 p-2">HỆ THỐNG</span>
                    </div>
                    </li>
                    <hr class="horizontal light mt-0 mb-2">
                    <li class="nav-item navbar-expand-xs">
                        <a class="nav-link text-white" href="<?= site_url('login/logout'); ?>" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">
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
                © Production Management System
            </div>
        </div>
    </footer>
  <!--   Core JS Files   -->
      <!-- jQuery MUST be loaded FIRST -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="<?= site_url('asset/backend/assets/js/core/popper.min.js'); ?>"></script>
    <script src="<?= site_url('asset/backend/assets/js/core/bootstrap.min.js'); ?>"></script>
    <script src="<?= site_url('asset/backend/assets/js/plugins/perfect-scrollbar.min.js'); ?>"></script>
    <script src="<?= site_url('asset/backend/assets/js/plugins/smooth-scrollbar.min.js'); ?>"></script>
    <script src="<?= site_url('asset/backend/assets/js/plugins/chartjs.min.js'); ?>"></script>
        <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script>
    // Define safe versions of Material Dashboard functions before loading the library
    window.navbarColorOnResize = function() {
      // Safe no-op function to prevent errors when navbar elements don't exist
      if (typeof referenceButtons !== 'undefined' && referenceButtons !== null && referenceButtons.classList) {
        // If elements exist, we could call original logic here, but for now just prevent errors
        return;
      }
    };

    window.sidenavTypeOnResize = function() {
      // Safe version that handles missing elements gracefully
      let elements = document.querySelectorAll('[onclick="sidebarType(this)"]');
      if (window.innerWidth < 1200) {
        elements.forEach(function(el) {
          if (el && el.classList) {
            el.classList.add('disabled');
          }
        });
      } else {
        elements.forEach(function(el) {
          if (el && el.classList) {
            el.classList.remove('disabled');
          }
        });
      }
    };

    $(document).ready(function() {
      // Load Material Dashboard JS after jQuery and DOM is ready
      var script = document.createElement('script');
      script.src = '<?= site_url('asset/backend/assets/js/material-dashboard.js?v=2.1.2'); ?>';
      script.onload = function() {
        console.log('Material Dashboard v2 loaded');
        // Initialize components after script loads
        initializeDashboardComponents();
      };
      script.onerror = function() { console.warn('Material Dashboard v2 failed to load'); };
      document.head.appendChild(script);
    });

    function initializeDashboardComponents() {
      // Initialize scrollbar for Windows
      var win = navigator.platform.indexOf('Win') > -1;
      if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = { damping: '0.5' };
        if (typeof Scrollbar !== 'undefined') {
          Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
      }
    }
  </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

  <script>
// Show flashdata messages (following Project pattern exactly)
    $(document).ready(function() {
      // Kiểm tra URL parameter ?msg= và giá trị cụ thể
      var urlParams = new URLSearchParams(window.location.search);
      var msgType = urlParams.get('msg'); // 'success' hoặc 'error'
      
      // Kiểm tra sessionStorage để tránh hiển thị lại khi refresh
      var toastShown = sessionStorage.getItem('toast_shown_' + window.location.pathname);
      
      // Chỉ hiển thị toast khi:
      // 1. Có msg parameter trong URL (redirect từ action)
      // 2. Chưa được hiển thị trong session này
      if (msgType && !toastShown) {
        <?php if($this->session->flashdata('success')): ?>
        // Chỉ hiển thị success nếu msg=success
        if (msgType === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: '<?= addslashes($this->session->flashdata('success')) ?>',
            showConfirmButton: true,
            confirmButtonText: 'OK',
            confirmButtonColor: '#17ad37',
            timer: 3000,
            timerProgressBar: true
          });
          
          // Đánh dấu đã hiển thị
          sessionStorage.setItem('toast_shown_' + window.location.pathname, 'true');
          
          // Xóa msg parameter khỏi URL
          window.history.replaceState({}, document.title, window.location.pathname);
        }
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
        // Chỉ hiển thị error nếu msg=error
        if (msgType === 'error') {
          Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: '<?= addslashes($this->session->flashdata('error')) ?>',
            showConfirmButton: true,
            confirmButtonText: 'Đóng',
            confirmButtonColor: '#dc3545'
          });
          
          // Đánh dấu đã hiển thị
          sessionStorage.setItem('toast_shown_' + window.location.pathname, 'true');
          
          // Xóa msg parameter khỏi URL
          window.history.replaceState({}, document.title, window.location.pathname);
        }
        <?php endif; ?>
      }
      
      // Xóa flag khi navigate sang trang khác (cho phép toast hiện lại lần sau)
      window.addEventListener('beforeunload', function() {
        sessionStorage.removeItem('toast_shown_' + window.location.pathname);
      });
    });

    if (document.getElementById("chart-bars")) {
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
}

if (document.getElementById("chart-line")) {
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
      }
  </script>

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <!-- Material Dashboard JS is already loaded in head section with error handling -->

</body>

</html>